<?php

/**
 * Description of WishesModel
 * Tento model zajistuje generovani a nasledne checkovani potvrzovacich
 * retezcu.
 * Vhodne pro linky, ktere jsou pristupne jen docasne pro toho, kdo zna dany
 * hash
 * @author Pavel Vais
 */
class WishesModel extends DML {

	const ROWS_PER_PAGE = 10;

	public function __construct()
	{

		parent::__construct( 'wishes' );
		$this->ci->load->helper( 'string' );
		$this->tags = array();
	}

	public function is_wish_url_avaible($url, $author_id)
	{
		$this->db->select( "1", TRUE )
			   ->where( "url", $url )
			   ->where( "author_id", $author_id );

		return $this->get_one() != FALSE ? TRUE : FALSE;
	}

	public function get_wish_wall($last_id, $number)
	{
		$settings = array(
		    'subscr_multiplier' => 1,
		    'fcb_multiplier' => 5.5,
		    'length_multiplier' => 0.022
		);

		/*
		 * podminky:
		 * Od jednoho ID (group) jedna zprava (v debugu vypnute)
		 * seskupeni dle data triggeru
		 * 
		 * vysledek zlimitovat dle argumentu $number
		 */

		$this->db->order_by( "event_time DESC" )
			   ->limit( $number, $last_id )
			   ->where( 'event_time <', 'NOW() + 4 weeks', TRUE )
			   ->where( 'event_time <> ', '0000-00-00 00:00:00' )
			   ->where( 'visible', 1 )
			   ->where( 'deleted', 0 )
			   ->join( "users", $this->table_info->get_table_name() . ".author_id = users.id" )
			   ->select( $this->table_info->get_table_name() . ".*, users.username" );

		$result = $this->get();

		if ( $result == false )
			return false;

		$sm = new SubscribersModel();
		$ids = array();

		foreach ( $result as $row )
		{
			$ids[] = $row->id;
		}

		$subscribers_per_id = $sm->count_subscribers( $ids );
		$priorities = array();
		$now = new DateTime();
		//$now_ts = strtotime($now);
		foreach ( $result as &$row )
		{
			$priority = 0;
			$row->subscribers_count = $subscribers_per_id->{"wish_id_" . $row->id};
			$row->subscribed = false;
			if ( User::get_id() > 0 )
			{
				$row->subscribed = $sm->is_subscriber_exists( $row->id, User::get_id() );
			}
			$datetime2 = new DateTime( $row->event_time );
			//$datetime2_ts = strtotime($datetime2);
			if ( $now > $datetime2 )
				$priority = -100;
			else
			{
				$interval = $now->diff( $datetime2 );
				$priority -= abs( $interval->d ) * 10;
			}

			//

			$priority += $row->subscribers_count * $settings['subscr_multiplier'];
			$priority += strlen( $row->message ) * $settings['length_multiplier'];
			$priority += $row->facebook_shares * $settings['fcb_multiplier'];

			$row->priority = $priority;
			$priorities[] = $priority;
		}

		array_multisort( $priorities, SORT_DESC, $result );
		return $result;
	}

	public function get_all_wishes($page = 1)
	{
		if ( $page != false )
			$this->db->limit( self::ROWS_PER_PAGE, self::ROWS_PER_PAGE * ($page - 1) );

		$this->_join_username();
		$this->db->order_by( "id DESC" );
		$result = $this->get();

		if ( !$result )
			return false;

		$this->_join_subscribers_count( $result );
		;
		return $result;
	}

	public function get_random_wish($without_id = null)
	{
		$this->db->limit( 1 )
			   ->where( 'event_time <', 'NOW() + 2 weeks' )
			   ->where( 'event_time >', 'NOW()', FALSE )
			   ->where( 'event_time <> ', '0000-00-00 00:00:00' )
			   ->where( 'visible', 1 )
			   ->where( 'deleted', 0 )
			   ->order_by( '', 'random' )
			   ->join( "users", $this->table_info->get_table_name() . ".author_id = users.id" )
			   ->select( $this->table_info->get_table_name() . ".*, users.username" );

		if ( $without_id != null )
			$this->db->where( $this->table_info->get_table_name() . '.id <>', $without_id );

		$return = $this->get_one();
		if ( $return == FALSE )
			return FALSE;
		$sm = new SubscribersModel();

		if ( User::is_logged_in() )
			$return->subscribed = $sm->is_subscriber_exists( $return->id, $this->tank_auth->get_user_id() );
		else
			$return->subscribed = false;

		$return->subscribers_count = $sm->count_subscribers_per_wish( $return->id );
		return $return;
	}

	public function get_full_wish($id)
	{
		$this->db->where( $this->table_info->get_table_name() . '.id', $id )
			   ->join( "users", $this->table_info->get_table_name() . ".author_id = users.id" )
			   ->select( $this->table_info->get_table_name() . ".*, users.username" );

		$return = $this->get_one();
		if ( $return == FALSE )
			return FALSE;

		$sm = new SubscribersModel();
		if ( User::is_logged_in() > 0 )
			$return->subscribed = $sm->is_subscriber_exists( $return->id, $this->tank_auth->get_user_id() );
		else
			$return->subscribed = false;

		$return->subscribers_count = $sm->count_subscribers_per_wish( $return->id );
		return $return;
	}

	public function is_wish_belongs($user_id, $wish_id)
	{
		$this->db->where( 'author_id', $user_id )
			   ->where( 'id', $wish_id )
			   ->select( 'id' );

		return $this->get_one() == FALSE ? FALSE : TRUE;
	}

	public function search_wish($search_term)
	{
		$this->db->like( 'title', $search_term )
			   ->or_like( 'message', $search_term )
			   ->select( 'title,message' )
			   ->where( 'deleted', 0 )
			   ->where( 'visible', 1 )
			   ->limit( 5 );
		return $this->get();
	}

	public function get_wishes_by_month($year, $month)
	{
		$this->db->where( "YEAR(event_time)", $year, TRUE )
			   ->where( "MONTH(event_time)", $month, TRUE )
			   ->where( 'visible', 1 )
			   ->order_by( "event_time" );

		return $this->get();
	}

	public function get_wishes_by_day($year, $month, $day)
	{
		$date = mktime( null, null, null, $month, $day, $year );
		$date2 = mktime( null, null, null, $month, $day + 1, $year );

		$this->db->where( "event_time >=", DMLHelper::int2date( $date ) )
			   ->where( "event_time <", DMLHelper::int2date( $date2 ) )
			   ->order_by( "event_time" );
		$this->_join_username();
		$result = $this->get();

		if ( $result !== FALSE )
		{
			$this->_join_subscribers_count( &$result );
		}


		return $result;
	}

	/**
	 * Získá jednotlive prani pomoci ID
	 * @param int $id
	 * @return type
	 */
	public function get_wish_by_id($id)
	{

		$this->db->select( $this->table_info->get_table_name() . ".*, users.username" );
		$this->db->join( "users", $this->table_info->get_table_name() . ".author_id = users.id" );
		$this->db->where( $this->table_info->get_table_name() . ".id", $id );

		if ( $return = $this->get_one() )
		{
			$adr = new AddresseeModel();
			$return->emails = $adr->get_addresses( $id );
		}
		return $return;
	}

	/**
	 * Ziska jednotlive prani pomoci uid (pouzivane na prenos v url
	 * @param String $uid
	 * @return type
	 */
	public function get_wish_by_uid($uid)
	{

		$this->db->select( $this->table_info->get_table_name() . ".*, users.username, schemes.name AS scheme_name" );
		$this->db->join( "users", $this->table_info->get_table_name() . ".author_id = users.id" );
		$this->db->join( "schemes", $this->table_info->get_table_name() . ".scheme_id = schemes.id" );
		$this->db->where( $this->table_info->get_table_name() . ".unique_id", $uid );

		if ( $return = $this->get_one() )
		{
			$adr = new AddresseeModel();
			$return->emails = $adr->get_addresses( $return->id );
			$this->_join_subscribers_count( &$return );
		}

		return $return;
	}

	/**
	 * Ziska prani vztahujici se k danemu userovi
	 * @param type $user_id
	 * @param type $include_deleted
	 * @param type $include_private
	 * @return boolean
	 */
	public function get_wishes_by_user_id($user_id, $include_deleted = FALSE, $include_private = TRUE)
	{
		$this->db->select( $this->table_info->get_table_name() . ".*, users.username" );
		$this->db->join( "users", $this->table_info->get_table_name() . ".author_id = users.id" );
		$this->db->join( "schemes", $this->table_info->get_table_name() . ".scheme_id = schemes.id" );
		$this->db->where( "author_id", $user_id )
			   ->order_by( "event_time DESC" );

		if ( !$include_deleted )
			$this->db->where( "deleted", 0 );

		if ( !$include_private )
			$this->db->where( "visible", 1 );

		$result = $this->get();

		if ( $result == false )
			return FALSE; //= Nic nenalezeno

		$adr = new AddresseeModel();
		$sm = new SubscribersModel();

		$count = $sm->count_subscribers( $result );

		foreach ( $result as $key => &$wish )
		{
			$wish->subscribers_count = $count->{"wish_id_" . $wish->id};
			$wish->emails = $adr->get_addresses( $wish->id );
		}

		return $result;
	}

	private function _join_subscribers_count(&$result)
	{
		$sm = new SubscribersModel();
		$count = $sm->count_subscribers( $result );

		if ( !is_array( $result ) )
			$result->subscribers_count = $count->{"wish_id_" . $result->id};
		else
			foreach ( $result as $key => &$wish )
			{
				$wish->subscribers_count = $count->{"wish_id_" . $wish->id};
				//$wish->emails = $adr->get_addresses( $wish->id );
			}

		return $result;
	}

	public function get_wishes_by_subscribed_user_id($user_id)
	{
		$this->table_info->change_table_name( "subscribers" );
		$this->db->select( "wishes.*, users.username" )
			   ->join( "wishes", $this->table_info->get_table_name() . ".wish_id = wishes.id" )
			   ->join( "users", "users.id = wishes.author_id" )
			   ->where( "user_id", $user_id );

		$result = $this->get();


		if ( $result != false )
		{
			$sm = new SubscribersModel();

			$count = $sm->count_subscribers( $result );

			foreach ( $result as $key => &$row )
			{
				$row->subscribers_count = $count->{"wish_id_" . $row->id};
			}
		}

		return $result;
	}

	/**
	 * Funkce na "smazani" pranicka.
	 * Prani v databazi vzdy zustane, nicmene se zmeni na deleted,
	 * neni tudis dostupny pres get funkce vyuzivajici stranky vyjma administrace
	 * @param int $id
	 * @return type
	 */
	public function hide_wish($id)
	{
		$this->db->where( 'id', $id );
		return $this->add_data( "deleted", 1 )
					 ->update();
	}

	/**
	 * Urcuje, jestli je pranicko stale aktivni nebo ne
	 * @param type $id
	 * @return type
	 */
	public function is_wish_available($id)
	{
		$this->db->where( 'id', $id )
			   ->where( 'event_time >=', "NOW()", FALSE );

		return $this->get_one() == FALSE ? FALSE : TRUE;
	}

	private function _join_username()
	{
		$this->db->select( "wishes.*, users.username" )
			   ->join( "users", "users.id = wishes.author_id" );
		return $this;
	}

	/**
	 * Natvrdo smazani prani, misto teto funkce doporucuji pouzivat "hide_wish()" 
	 * funkci
	 * @param type $id
	 * @param type $uid
	 * @param type $owner_id
	 * @return boolean
	 */
	public function delete($id, $uid, $owner_id = null)
	{
		//= Overeni, jestli takove prani existuje
		//= (musi se udat jak ID tak uid
		$this->db->where( 'id', $id )
			   ->where( 'unique_id', $uid )
			   ->select( 'id' );

		if ( $owner_id != null )
			$this->db->where( 'author_id', $owner_id );

		if ( $this->get_one() != false )
		{
			$sm = new SubscribersModel();
			$sm->delete( $id );
			$result = $this->db->where( 'id', $id )
				   ->delete( $this->table_info->get_table_name() );
			$this->log_operation( $result );
			return $result;
		}
		else
		{
			$this->set_error( "Přání nebylo nalezeno.", DMLException::ERROR_NUMBER_NOT_FOUND );
			return false;
		}
	}

	/**
	 * Funkce slouzi k vypisu vsech prani vstahujici se k nejakemu datovemu 
	 * rozpeti.
	 * Funkce je vyuzivana pri rozesilani upozorneni
	 * @param type $date_of_trigger
	 * @param type $time_offset
	 * @return type
	 */
	public function get_wishes_by_time_diff($date_of_trigger, $time_offset)
	{
		$datetime = strtotime( $date_of_trigger . " + $time_offset minutes" );
		$datetime = date( 'Y-m-d H:i:s', $datetime );

		$this->db->where( "event_time >=", $date_of_trigger )
			   ->where( "event_time <", $datetime )
			   ->where( "deleted", 0 );

		$this->db->select( "wishes.*, users.username AS author_username" )
			   ->join( "users", "users.id = wishes.author_id" );

		$result = $this->get();
		if ( $result !== FALSE )
		{
			foreach ( $result as &$row )
			{
				//= pro kazde prani zijstit email a fcb idcka vsech subscriberu
				$sm = new SubscribersModel();
				$row->subscribers = $sm->get_subscribers( $row->id, true );
			}
		}

		return $result;
	}

	/**
	 * @todo Pri nezdaru vymazat prani!
	 * @return boolean
	 */
	public function save_wish()
	{

		$adr = new AddresseeModel();
		$emails = $this->get_data_value( "addressees" );
		$this->delete_data_by_name( "addressees" );

		$title = $this->get_data_value( "title" );
		$url = $this->get_data_value( "url" );

		$saved_error = 0;

		if ( $this->save() != FALSE )
		{

			$this->last_id = $this->db->insert_id();

			//= Generovani unikatniho ID, pod kterym je dane pranicko dostupne
			$uid = $this->generate_uid( $this->last_id );
			$this->db->where( "id", $this->last_id );
			$this->add_data( "unique_id", $uid );
			$this->update();


			if ( $emails != null )
			{
				if ( !is_array( $emails ) )
					$emails = array($emails);

				foreach ( $emails as $email )
				{
					if ( !$adr->add_addressee( $this->last_id, $email ) ) //= ulozim kazdy email
					{
						$saved_error++;
					}
				}
			}
		}
		else
		{
			return FALSE;
		}


		if ( $saved_error > 0 )
		{
			$this->set_error( "Nezapsali se všechni adresáti! ($saved_error z " . count( $emails ) . ")" );
			return FALSE;
		}

		return $uid;
	}

	/**
	 * Nastavi vsechna dulezita data pro ulozeni prani
	 * @param type $author_id
	 * @param type $title
	 * @param type $message
	 * @param type $url
	 * @param type $event_time
	 * @param type $addressee
	 */
	public function set_data($author_id, $title, $message, $url, $addressee = null, $event_time = null, $scheme_id = 1, $visible = 1)
	{

		$this->fetch_data( array(
		    'author_id' => $author_id,
		    'title' => $title,
		    'message' => $message,
		    'url' => $url == "" ? null : $url,
		    'event_time' => $event_time == "" ? null : $event_time,
		    'addressees' => $addressee,
		    'created' => DMLHelper::now( TRUE ),
		    'scheme_id' => $scheme_id,
		    'visible' => $visible
		) );
	}

	public function get_url($id)
	{

		$this->db->select( "unique_id,title,url" );
		$this->db->where( "id", $id );
		$result = $this->get_one();

		if ( $result != false )
		{
			return $this->generate_url( $result->title, $result->url, $result->unique_id );
		}
		else
			return false;
	}

	/**
	 * Vygeneruje url dle zadanych parametru. Jestli je k dispozici url a uid, title nema vyznam
	 * Pokud se do $uid vlozi objekt - system predpoklada, ze je to activerow a 
	 * vscehny iformace si z toho vycte. Nemusi se tedy vkladat url a title
	 * @param String/Activerow $uid - pozor!! uid neni id!!! UID je unikatni 
	 * token, ktery je jedinecny a zaheslovany
	 * @param String $title
	 * @param String $url
	 * @return String
	 */
	public function generate_url($uid, $url = null, $title = null)
	{
		if ( is_object( $uid ) )
		{
			$title = $uid->title;
			$url = $uid->url;
			$uid = $uid->unique_id;
		}
		//$id = $this->decode_id($id); //= ID se zakoduje. Pote je uz nerozkodovatelny

		$this->load->helper( array('text', 'url') );
		return url_title( convert_accented_characters( $url == null ? $title . "-" . $uid : $url . "-" . $uid  ) );
	}

	public function regenerate_url_facebook_shares($url)
	{
		$string = $url;
		$find = '-';
		$replace = '/';
		$result = preg_replace( strrev( "/$find/" ), strrev( $replace ), strrev( $string ), 1 );
		return strrev( $result );
	}

	public function update_view_count($wish_id)
	{
		$this->db->where( "id", $wish_id )
			   ->set( "viewed", "viewed + 1", FALSE )
			   ->update( $this->table_info->get_table_name() );

		return true;
	}

	/**
	 * Vygeneruje unikatni ID pomoci IDcka. Je overeno, ze prvnich 10000 zaznamu
	 * maji vzdy jine uid. Proto neni treba zpetne kontrolovat, jestli dane uid
	 * je v db unikatni. V druhem pripade se generuje do te doby, dokud opravdu
	 * neni unikatni
	 * @param int $id - ID pranicka
	 * @return Boolean
	 */
	public function generate_uid($id)
	{
		do
		{
			$uid = $this->_decode_id( $id );
			$id = mt_rand();
		}
		while ( $id > 10000 && $this->_is_uid_unique( $uid ) == FALSE );

		return $uid;
	}

	/**
	 * Predstavuje generator nahodnych znaku dle zadaneho ID (v nasem pripade
	 * ID pranicka).
	 * Vygeneruje $length dlouhy retezec. Pri sestimistnem retezci je zarucena
	 * unikatnost pro prvnich 10000 (25000) prani. 
	 * @param int/String $id - seed, dle ktereho se vygeneruje
	 * @return string - unikatni retezec
	 */
	private function _decode_id($id)
	{
		$length = 6;
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

		mt_srand( $id );
		$uid = substr( $id, 0, 1 );
		for ( $i = 0; $length > $i; $i++ )
		{
			$uid .= $characters[mt_rand( 0, strlen( $characters ) - 1 )];
		}

		return $uid;
	}

	/**
	 * Vrati posledni vlozene ID
	 * @return type
	 */
	public function get_last_inserted_id()
	{
		return isset( $this->last_id ) ? $this->last_id : null;
	}

	/**
	 * Zjisti, jestli je retezec opravdu unikatni 
	 * (podiva se do databaze)
	 * @param type $uid
	 * @return type
	 */
	private function _is_uid_unique($uid)
	{
		$this->db->where( "unique_id", $uid );
		return $this->get_one() == FALSE ? TRUE : FALSE;
	}

	public function add_facebook_count($wish_uid)
	{
		$this->db->where( "unique_id", $wish_uid )
			   ->set( "facebook_shares", "facebook_shares + 1", FALSE )
			   ->update( $this->table_info->get_table_name() );

		return true;
	}

	public function count_wishes()
	{
		return $this->count_rows();
	}

	public function filter_wishes($keywords, $where_array, $page, $just_count_it = false)
	{
		if ( $keywords !== "" )
		{
			$this->db->like( "LOWER(users.username)", strtolower( $keywords ) )
				   ->or_like( "LOWER(title)", strtolower( $keywords ) )
				   ->or_like( "LOWER(message)", strtolower( $keywords ) );
		}
		$this->_join_username();
		if ( $where_array !== null )
		{
			$this->db->where( $where_array );
		}

		if ( $just_count_it )
			return $this->count_wishes();

		$this->db->limit( self::ROWS_PER_PAGE, self::ROWS_PER_PAGE * ($page - 1 ) )
			   ->order_by( "id DESC" );

		if ( !$result = $this->get() )
			return false;
		else
			$this->_join_subscribers_count( $result );

		return $result;
	}

	public function count_wishes_by_date($date = "NOW()")
	{
		$this->db->where( 'DATE(event_time) ', "DATE($date)", FALSE );
		return $this->count_wishes();
	}

	public function count_wishes_per_day($offset = "7 DAY", $order_direction = "DESC")
	{
		$this->db->select( "created,COUNT(id) as pocet" )
			   ->where( "DATE(created) >", "DATE_SUB(CURDATE(),INTERVAL $offset)", FALSE )
			   ->group_by( "DATE(created)", FALSE )
			   ->order_by( "created $order_direction" );

		return $this->get();
	}

	/**
	 * 
	 */
	public function get_not_approved_wishes()
	{
		$this->db->where( "status", 0 );

		return $this->get();
	}

	/**
	 * 1 = approved
	 * 0 = waiting_for_approve
	 * -1 = not_approved
	 * @param type $wish_id
	 * @param type $approved
	 * @return type
	 */
	public function set_wish_status($wish_id, $approved = 1)
	{
		$this->add_data( "status", $approved );
		$this->db->where( "id", $wish_id );

		return $this->update();
	}

	public function inline_edit($id, $request, $value)
	{
		$this->db->where( "id", $id );
		return $this->add_data( $request, $value )
					 ->add_data( "status", 0 )
					 ->update();
	}

	/**
	 * Zmeni hodnotu ve sloupci u daneho pranicka.
	 * Pozor! jelikoz je to public funkce, misto ID se vklada
	 * UID!
	 * @param type $uid
	 * @param type $column
	 * @param type $value
	 * @return type
	 */
	public function change_value($uid, $column, $value)
	{
		$this->db->where( "unique_id", $uid );
		return $this->add_data( $column, $value )
					 ->update();
	}

}

?>

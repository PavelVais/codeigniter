<?php

/**
 * @author Pavel Vais
 */
class SubscribersModel extends DML {

	public function __construct()
	{

		parent::__construct( 'subscribers' );
	}

	/**
	 * Získa IDcka subscriberu pro dane prani
	 * @param int $wishes_id
	 * @return Result 
	 */
	public function get_subscribers($wishes_id,$detailed = false)
	{
		$this->db->where( "wish_id", $wishes_id );

		if ($detailed)
		{
			$this->db->join("users","users.id = ".$this->table_info->get_table_name().".user_id")
				   ->select("users.id,users.username,users.email,users.fcb_id");
		}
		return $this->get();
	}

	/**
	 * Spocita vscehny subscribery, ktery se vstahujou k vlozenemu
	 * id pranicka, popripade k vlozenemu poli idcek pranicek.
	 * @param int/ array(int) $wishes_id
	 * @return result 
	 */
	public function count_subscribers($wishes_id)
	{

		if ( !is_array( $wishes_id ) )
			$wishes_id = array($wishes_id);


		$query = "SELECT ";
		$pocet = count( $wishes_id );
		$index = 0;
		foreach ( $wishes_id as $w )
		{
			if ( isset( $w->id ) )
				$w = $w->id;


			$index++;
			$query .= "SUM(CASE WHEN `wish_id` = " . intval( $w ) . " THEN 1 ELSE 0 END) AS wish_id_$w ";
			if ( $index < $pocet )
				$query .= ", \n";
		}

		$query .= "\nFROM `" . $this->table_info->get_table_name() . "`";

		$result = $this->db->query( $query );
		parent::log_operation( $result );

		$res = $result->result();

		if ( is_array( $res ) )
			$res = $res[0];

		return $res;
	}

	public function delete($wish_id, $user_id = null)
	{
		$this->db->where( 'wish_id', $wish_id );

		if ( $user_id != null )
			$this->db->where( 'user_id', $user_id );

		$return = $this->db->delete( $this->table_info->get_table_name() );
		$this->log_operation( $return );

		return $return;
	}

	public function count_subscribers_per_wish($wish_id)
	{
		$return = $this->count_subscribers( $wish_id );
		return $return->{"wish_id_" . $wish_id};
	}

	public function subscribe_wish($user_id, $wish_id)
	{
		$data = array(
		    'wish_id' => $wish_id,
		    'user_id' => $user_id
		);

		return $this->fetch_data( $data )->save();
	}

	public function is_subscriber_exists($wish_id, $user_id)
	{
		if ($user_id == 0) return false;
		
		$this->db->where( 'user_id', $user_id )
			   ->where( 'wish_id', $wish_id )
			   ->select( 'id' );

		return $this->get_one() == FALSE ? FALSE : TRUE;
	}

	public function save_subscribers($wish_id, $user_id)
	{
		$this->add_data( array(
		    'wish_id' => $wish_id,
		    'user_id' => $user_id
		) );


		return $this->save();
	}

	/**
	 * Pouziva se?????
	 * @param type $emails
	 * @return type
	 */
	public function update_emails($emails)
	{
		if ( !is_array( $emails ) )
		{
			$emails = array($emails);
		}
		$ids = array();

		//= Podivat se, kolik z nich existuje
		$this->db->where_in( "email", $emails );

		//= Musime model preorientovat na email tabulku
		$this->table_info->name = "emails";

		if ( $result = $this->get() )
		{
			//= Musime vyradit existujici z insertu
			foreach ( $emails as $key => $email )
			{
				foreach ( $result as $result_email )
				{
					$ids[] = $result_email->id;
					if ( $email == $result_email->email )
					{
						unset( $emails[$key] );
						break;
					}
				}
			}
		}
		$index = 0;
		$array = array();
		if ( $emails != null )
		{
			//= Pokud jsou vscehny emailu uz v databazi, nedojde k zadnemu zapisu
			foreach ( $emails as $email )
			{
				$index++;
				$array[] = array("email" => $email);
			}

			$this->add_data( $array );
			$this->save_batch();


			//= Zjistime idcka insertovanych emailu
			$this->db->select( "id" );
			$this->db->where_in( "email", $emails );
			$result = $this->get();

			foreach ( $result as $email )
			{
				$ids[] = $email->id;
			}
		}

		return $ids;
	}

}

?>

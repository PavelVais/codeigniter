<?php

/**
 * Description of ConfirmModel
 * Tento model zajistuje generovani a nasledne checkovani potvrzovacich
 * retezcu.
 * Vhodne pro linky, ktere jsou pristupne jen docasne pro toho, kdo zna dany
 * hash
 * @author Pavel Vais
 */
class ConfirmModel extends DML\Base
{
	/**
	 * Jeden tyden v sekundach 
	 */

	const EXPIRATION_WEEK = 604800;

	/**
	 * Jeden den v sekundach
	 */
	const EXPIRATION_DAY = 86400;

	/**
	 * Jedna hodina v sekundach 
	 */
	const EXPIRATION_ONE_HOUR = 3600;

	/**
	 * Udava nazev skupiny, ktera se aplikuje na vsechny mozne funkce.
	 * @var String
	 */
	private $name;

	/**
	 * Pridava tagy k jednotlivym potvrzeni
	 * @var Array
	 */
	private $tags;

	/**
	 * Konstruktor tridy,
	 * nacte helper string. 
	 */
	public function __construct()
	{

		parent::__construct( 'confirm' );
		$this->ci->load->helper( 'string' );
		$this->tags = array();
	}

	/**
	 * Vygeneruje hash, ktery nasledne ulozi do databaze.
	 * Pokud se vse povede jak ma, vrati se v returnu dany $hash.
	 * V druhem pripade se vraci FALSE
	 * @param String $tags_check - Podiva se, jestli neexistuje pro dane tagy jiz
	 * udaj. Pokud ano, vrati FALSE a errorove cislo 506 (DUPLICATED)<br>
	 * Pozor: tagy musi mit stejne poradi jak jsou ulozene v databazi!
	 * @param int $expiration - expirace udana ve vterinach
	 * @param String(16) $group_name - v databazi muze byt vice druhu confirmu.
	 * timto se odlisuji.
	 * @return String / FALSE (pri neuespechu) 
	 */
	public function generate($tags_check = FALSE, $expiration = self::EXPIRATION_WEEK, $group_name = null)
	{
		$hash = $this->_generate_hash();
		$exp = $this->_generate_expiration( $expiration );
		$data = array(
			 'hash' => $hash,
			 'expiration' => DMLHelper::int2date( $exp )
		);
		$this->fetchData( $data );
		if ( $group_name != null )
			$this->addData( array("group" => $group_name) );


		if ( $tags_check && !empty( $this->tags ) )
		{
			$this->db->where( 'tags', $this->_prepare_tags() );
			if ( $group_name != null )
				$this->db->where( "group", $group_name );
			$this->db->where( 'disabled', 0 );

			if ( $this->dbGetOne() != FALSE )
			{
				$this->set_error( "Pro tag(y) " . $this->_prepare_tags() . " již potvrzovací kód existuje.", DBException::ERROR_NUMBER_DUPLICATED );
				return FALSE;
			}

			$this->addData( array('tags' => $this->_prepare_tags()) );
		}


		return $this->save() ? $hash : FALSE;
	}

	/**
	 * Prida tag/y.
	 * @param String/Array $tags - tagy muzete psat do stringu oddelene carkou
	 * nebo je vlozit do arraye
	 * @return \ConfirmModel 
	 */
	public function add_tag($tags)
	{
		if ( !is_array( $tags ) )
		{
			if ( strpos( $tags, "," ) === FALSE )
				$tags = array($tags);
			else
				$tags = explode( ',', $tags );
		}

		foreach ( $tags as $tag )
		{
			$this->tags[] = $tag;
		}

		return $this;
	}

	/**
	 * Dle vlozeneho hashe zjisti, jestli dane potvrzeni je v databazi
	 * @param String $hash
	 * @param boolean $disable - po zdarnem zjisteni se automaticky
	 * potvrzeni deaktivuje
	 * @param boolean $delete - po zjisteni se potvrzeni automaticky smaze
	 * @param boolean $disable_when_expired - Pokud hash existuje ale je jiz stary,
	 * tak se deaktivuje
	 * @param boolean $delete_when_expired -  - Pokud hash existuje ale je jiz stary,
	 * tak se smaze
	 * @return  FALSE pri spatnem nebo proslem confirmu
	 * active row pri spravnem confirmu (tags,group,expiration,hash,id)
	 */
	public function check_confirm($hash, $disable = FALSE, $delete = FALSE, $disable_when_expired = FALSE, $delete_when_expired = FALSE)
	{
		$this->db->where( "hash", $hash );
		$this->_check_group_where();
		$result = $this->dbGetOne();

		if ( $result == FALSE )
			return FALSE;

		$d1 = new DateTime( $result->expiration );
		$d2 = new DateTime();

		if ( $d1 < $d2 )
		{
			if ( $delete_when_expired )
				$this->delete_confirm( $result->id );
			elseif ( $disable_when_expired )
				$this->disable_confirm( $result->id );

			return FALSE;
		}

		if ( $delete )
			$this->delete_confirm( $result->id );
		elseif ( $disable )
			$this->disable_confirm( $result->id );

		return $result;
	}

	/**
	 * Pokud zadame nazev skupiny, pote se vsechny
	 * nasledujici funkce budou vstahovat POUZE pro danou skupinu.
	 * (vymazani vsech potvrzeni probehne pouze v dane skupine..)
	 * @param String $name
	 * @return \ConfirmModel 
	 */
	public function set_group($name)
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * Zneaktivni dane potvrzeni
	 * @param int $id
	 * @return void 
	 */
	public function disable_confirm($id)
	{
		$this->db->where( "id", $id );
		$this->addData( "disabled", 1 );

		$this->_check_group_where();

		return $this->update();
	}

	/**
	 * Smaze dane potvrzeni
	 * @param int $id
	 * @return type 
	 */
	public function delete_confirm($id)
	{
		$this->db->where( "id", $id );

		$this->_check_group_where();

		return $this->db->delete( $this->name );
	}
	
	public function get_confirm_by_tags($tags)
	{
		$this->db->where("tags",$tags);
		return $this->dbGet();
	}

	/**
	 * Smaze vscehny confirmy z databaze, ktere uz maji
	 * po expiraci.
	 * Pokud jsou udany tagy nebo skupina, smazou se
	 * jen neaktivni confirmy v dane skupine / s danym tagem
	 */
	public function clear_old_confirms()
	{
		$this->db->where( "expiration <", "NOW()", FALSE );
		if ( !empty( $this->tags ) )
			$this->db->where( 'tags', $this->_prepare_tags() );

		$this->_check_group_where();
		$this->db->delete( $this->name );

		return $this;
	}

	public function get_tag_from_result($result, $index = 0)
	{
		if ( $result == FALSE )
			return FALSE;

		if ( is_object( $result ) && !empty( $result->tags ) )
		{
			$result = $result->tags;
		}

		$tags = explode( ",", $result );

		return isset( $tags[$index] ) ? $tags[$index] : $tags[0];
	}

	/**
	 * Prida podminku pro skupinu, paklize je skupina urcena
	 * @return \ConfirmModel 
	 */
	private function _check_group_where()
	{
		if ( $this->name != null )
			$this->db->where( "group", $this->name );

		return $this;
	}

	/**
	 * Vrati expiraci ve vterinach
	 * @param int $seconds
	 * @return int 
	 */
	private function _generate_expiration($seconds)
	{
		return time() + $seconds;
	}

	/**
	 * Vygeneruje 32 znaku md5 hashe 
	 */
	private function _generate_hash()
	{
		return random_string( 'unique' );
	}

	private function _prepare_tags()
	{
		if ( !empty( $this->tags ) )
		{
			return implode( ",", $this->tags );
		}

		return "";
	}

	/**
	 * Pokud vsechny tagy souhlasi, vraci TRUE v druhem pripade FALSE
	 * @param Array $one_tags
	 * @param Array $second_tags 
	 */
	private function _check_tags($one_tags, $second_tags)
	{
		if ( !is_array( $one_tags ) )
		{
			$one_tags = explode( ",", $one_tags );
		}

		if ( !is_array( $second_tags ) )
		{
			$second_tags = explode( ",", $second_tags );
		}

		foreach ( $one_tags as $tag )
		{
			$status = FALSE;
			foreach ( $second_tags as $key => $stag )
			{
				if ( $tag == $stag )
				{
					$status = TRUE;
					break;
				}
			}

			if ( !$status )
				return FALSE;

			unset( $second_tags[$key] );
		}

		return TRUE;
	}

	/**
	 * Smaze vsechny nastavene tagy a skupiny
	 */
	public function clear_data()
	{
		$this->tags = array();
		$this->name = null;
	}

}

?>

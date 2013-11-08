<?php

/**
 * Description of VariablesModel
 * @author Pavel Vais
 * 
 */
class VariablesModel extends DML
{

	private $without_user;

	/**
	 * Konstruktor tridy
	 */
	public function __construct()
	{

		parent::__construct( 'variables' );
		$this->without_user = false;
	}

	public function omit_user()
	{
		$this->without_user = TRUE;
		return $this;
	}

	/**
	 * Vrati vsechny promenne vstahujici se k danemu user uctu
	 * Vysledek je vzdy "name" a "value" (nazev promenne a hodnota)
	 * Nerozlisuje se zde, jestli jde o value_int nebo value_string
	 * @param int $user_id
	 * @return type
	 */
	public function get_all_variables($user_id)
	{
		$this->db->where( 'user_id', $user_id );
		if ( !$result = parent::dbGet() )
			return false;

		foreach ( $result as &$r )
		{
			if ( $r->value_int == "" || $r->value_int == null )
				$r->value = $r->value_string;
			else
				$r->value = $r->value_int;
		}
		return $result;
	}

	/**
	 * Ulozi promennou do databaze. Dle typu promenny se ulozi bud do sloupce
	 * 'value_int' nebo 'value_string'
	 * @param String $name - Nazev promenne, pod kterou se hodnota ulozi
	 * @param String/int $value - hodnota promenne. Pokud vlozite do stringu
	 * "+" nebo "-" system automaticky hodnotu pricte nebo odecte hodnote, ktera
	 * je v systemu jiz zapsana ($value = "+5" -> pricte petku)
	 * @param int $user_id - Nepovinne. Pokud neni udaj urcen,
	 * pouzije se aktualni prihlaseny uzivatel. Pokud se udaj uda, pouzije se 
	 * IDcko te osoby, ktera se do promenne napise. Nekontroluje to, zdali je IDcko platne!
	 * Pokud se uklada promenna k uzivateli, ale v systemu neni zadny zalogovan,
	 * vraci se FALSE a nic se neulozi.
	 * @return type
	 */
	public function set($name, $value, $user_id = null)
	{
		if ( is_bool( $value ) )
		{
			$value = ($value == TRUE ? 1 : 0 );
		}

		// Pokud hodnota neexistuje, vlozi se, jinak se updatne
		$result = $this->dbGet( $name, $user_id );
		$input_method = ($result === false ? 'save' : 'update');

		//= Podiva se, jestli to zacina + nebo -.
		//= V tom pripade se provede pricteni nebo odecteni hodnoty
		$operator = substr( $value, 0, 1 );
		$new_value = $value;

		if ( $operator == "+" || $operator == "-" )
		{
			if ( $input_method == "save" )
			{ //Pri savu to nahrubo vlozi dane cislo
				$new_value = substr( $value, 1 );
				$value = $new_value;
			}
			else
			{
				$new_value = is_numeric( $result ) ? ($operator == "+" ? $result + substr( $value, 1 ) : $result - substr( $value, 1 ) ) : $result . substr( $value, 1 );
			}
		}

		$this->addData( 'name', $name );
		$this->addData( is_numeric( $new_value ) ? 'value_int' : 'value_string', $new_value );
		$this->addData( is_numeric( $new_value ) ? 'value_string' : 'value_int', null );

		if ( $this->without_user == FALSE )
			if ( User::is_logged_in() || $user_id != null )
				$this->addData( 'user_id', $user_id == null ? \User::get_id() : intval( $user_id )  );
			else
			{
				$this->set_error( 'Proměnná se nemohla uložit. K systému není přihlášen žádný uživatel, ke kterému by se proměnná zapsala.', 500 );
				return false;
			}
		if ( $input_method == "update" )
		{
			$this->db->where( "name", $name );
			if ( $this->without_user == FALSE )
				$this->db->where( "user_id", $user_id == null ? \User::get_id() : intval( $user_id )  );
			else
				$this->db->where( "user_id is null", null, FALSE );
		}



		//UPDATE `variables` SET `name` = 'points', `value_string` = 'points+5', `value_int` = NULL, `user_id` = 1
		return $this->{$input_method}();
	}

	/**
	 * Ziska ulozenou informaci z databaze
	 * @param String $name - nazev promenne
	 * @param int $user_id - Pokud je $user_id NULL, pouzije se prave 
	 * nalogovany uzivatel, jinak se pouzije ID uzivatele v $user_id predanym
	 * @param boolean $return_active_record - Ma se vratit cely objekt, nebo 
	 * jen hodnota, v nem obsazena? (Default FALSE - vraci jen hodnotu)
	 * @return boolean/String/int - Vraci FALSE pri nenaleznuti udaju
	 * @todo Opatreni proti neprihlasenemu uzivateli
	 */
	public function get($name, $user_id = null, $return_active_record = false)
	{
		$this->db->where( 'name', $name );

		if ( $this->without_user == FALSE )
			$this->db->where( 'user_id', $user_id == null ? \User::get_id() : $user_id  );

		$result = parent::dbGetOne();
		if ( $result == false )
			return FALSE;

		if ( !$return_active_record )
		{
			return $result->value_int == null ? $result->value_string : $result->value_int;
		}
		else
			return $result;
	}

	/**
	 * Smaze promennou z databaze
	 * @param type $name
	 * @param type $user_id
	 * @return type
	 */
	public function delete($name, $user_id = null)
	{
		$this->db->where( 'name', $name );

		if ( $this->without_user == FALSE )
			$this->db->where( 'user_id', $user_id == null ? \User::get_id() : $user_id  );
		else
			$this->db->where( 'user_id', null );

		$result = $this->db->delete( $this->tableInfo->get_table_name() );
		$this->log_operation( $result );

		return $result;
	}

	/**
	 * Prejmenuje promennou. Diky tomu se nemusi mazat a nasledne vytvaret nova
	 * @param String $old_name - puvodni nazev promenne
	 * @param String $new_name - novy nazev promenne
	 * @param int/void $user_id - muze se urcit k jakemu user id to smeruje
	 * @return type - TRUE - bylo zmeneno, FALSE - nebylo
	 */
	public function rename($old_name, $new_name, $user_id = null)
	{
		$this->db->where( 'name', $old_name );

		if ( $this->without_user == FALSE )
			$this->db->where( 'user_id', $user_id == null ? \User::get_id() : $user_id  );

		$this->addData( "name", $new_name )
				  ->update();
		return $this->affected_rows() >= 1 ? TRUE : FALSE;
	}

	/**
	 * Smaze VSECHNY promenny vstahujici se k danemu uctu
	 * @param int $user_id [optional] - kdyz se nepouzije,
	 * veme se aktualni IDcko usera
	 * @return boolean
	 */
	public function purge($user_id = null)
	{
		if ( $user_id == null && User::is_logged_in() === false )
			return FALSE;

		if ( $user_id == null )
			$user_id = User::get_id();

		$this->db->where( 'user_id', $user_id );

		$result = $this->db->delete( $this->tableInfo->get_table_name() );
		$this->log_operation( $result );

		return $result;
	}

}

?>

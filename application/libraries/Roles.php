<?php

/**
 * Trida Roles
 * 
 */
class Roles
{

	/**
	 * Zasobnich vsech roli, nactenych z config/roles.php
	 * @var array 
	 */
	private $roles;

	/**
	 * Zasobnich vsech pravidel, nactenych z config/roles.php
	 * @var array 
	 */
	private $rules;

	/**
	 * Nazev role aktualniho uzivatele
	 * @var String 
	 */
	private $user_role;

	/**
	 * Slouzi k dotazovani databaze
	 * @var UsersModel
	 */
	private $usersModel;

	/**
	 * Status, ktery porovnavac vraci kdyz:
	 * V podmince je dana role vyslovne zakazana 
	 */
	const COMPARISON_FORBIDDEN = "failed";

	/**
	 * Status, ktery porovnavac vraci kdyz:
	 * V podmince je rodic nebo konkretni role vyslovene povolena
	 */
	const COMPARISON_ALLOWED = "allowed";

	/**
	 * Status, ktery porovnavac vraci kdyz:
	 * V podmince dana role neni obsazena
	 */
	const COMPARISON_NOT_FOUND = "not_found";

	/**
	 * Status, ktery porovnavac vraci kdyz:
	 * V podmince je dana role naleznuta (vyskyt "all", popripade nazvu rodice
	 * nebo konkretni role)
	 */
	const COMPARISON_FOUND = "found";

	/**
	 * Urceni ukazatele pro zakladni roli.
	 * Vetsinou pro neregistrovany uzivatele 
	 */
	const DEFAULT_ROLE = "others";

	/**
	 * 
	 * Konstruktor tridy
	 */
	function __construct()
	{
		User::init();
		$this->ci = & get_instance();
		$this->ci->load->config( 'roles' );

		//$this->ci->load->model( "tank_auth/users", 'users' );
		$this->usersModel = new UsersModel;
		$config = $this->ci->config->item( 'roles' );

		$this->roles = $config['roles'];
		$this->rules = $config['rules'];

		$this->reload_data();
	}

	/**
	 * V nekterych pripadech je nutne znovu nacist data, aby se aktualizoval
	 * status usera (je nalogovan?)
	 */
	public function reload_data()
	{
		if ( User::is_logged_in() )
		{
			$user_data = $this->usersModel->get_user_by_id( User::get_id(), TRUE );

			if ( $this->ci->config->item( 'roleTable', 'authorization' ) != '' )
				$user_data->role = User::get_role();

			if ( !isset( $user_data->role ) OR $user_data->role == "" OR $user_data->role == null )
			{
				$this->user_role = $this->_get_default_role();
			}
			else
			{
				$this->user_role = $user_data->role;
			}
		}
		else
		{
			$this->user_role = $this->_get_default_role();
		}
	}

	/**
	 * Tato metoda urci, jeslti muze uzivatel vykonat danou metodu.
	 * 
	 * @param type $rule - Tema podminek (wishes,users,news)
	 * @param type $method - Metoda vstahujici se k danemu tematu.<br>
	 * ("Muze editovat clanky? => {muze vykonat <metodu> v <tematu>?})
	 * @return boolean TRUE - Dany uzivatel muze vykonat konkretni metodu
	 * FALSE - Dany uzivatel ma zakazano vykonat konkretni metodu
	 */
	public function allowed($rule, $method)
	{
		$roles = $this->_get_role_dependency_array();
		$status = FALSE;

		$count = count( $roles ); //= Nikdy nedavat for to do s necachovanym countem!
		//= Rekurzivni prohledavani (od nejnizsiho stupne po nejvyssi)
		for ( $index = $count - 1; $index >= 0; $index-- )
		{
			$result = $this->_compare_rule_and_role( $rule, $method, $roles[$index], $roles[$index] == $this->user_role );

			switch ($result)
			{
				//= Pokud je vyslovene role povolena, vraci true
				case self::COMPARISON_ALLOWED:
					$status = TRUE;
					break;

				//= Pokud je role naleznuta, vraci true
				case self::COMPARISON_FOUND:
					$status = TRUE;
					break;

				//= Pokud je role vyslovene zakazana, status meni na FALSE
				case self::COMPARISON_FORBIDDEN:
					$status = FALSE;
					break;

				//= Pokud aktualni role nebyla naleznuta a je to posledni role,
				//= a nejde o administratora (all), vraci se false
				case self::COMPARISON_NOT_FOUND:
				default:

					if ( $index == 0 && !$status )
					{
						return $this->_get_role( $this->get_user_role() ) == "all" ? TRUE : FALSE;
					}
					break;
			}
		}
		return $status;
	}

	/**
	 * Vrati roli pro aktualniho uzivatele
	 * @return String 
	 */
	public function get_user_role()
	{
		return $this->user_role;
	}

	/**
	 * Vrati nazvy nadefinovanych roli<br>
	 * Vhodne do administrace, pri tvorbe novych uctu
	 * @return array 
	 */
	public function get_roles()
	{
		$roles = array();

		foreach ( $this->roles as $role => $value )
		{
			$roles[] = $role;
		}

		return $roles;
	}

	/**
	 * Vrati zakladni roli. Vetsinou do ni patri neregistrovani uzivatele
	 * @return String 
	 */
	private function _get_default_role()
	{
		foreach ( $this->roles as $role_name => $role_type )
		{
			if ( $role_type == self::DEFAULT_ROLE )
			{
				return $role_name;
			}
		}

		show_error( "Nastaveni roli neobsahuje zakladni roli pro neregistrovane uzivatele! (hodnota: " . self::DEFAULT_ROLE . ")" );
	}

	private function _get_role($role_name)
	{
		if ( !isset( $this->roles[$role_name] ) )
		{
			show_error( "Roles: role $role_name neni definovana." );
		}
		return $this->roles[$role_name];
	}

	private function _get_rule($rule_name, $rule_method = null)
	{
		if ( !isset( $this->rules[$rule_name] ) )
		{
			show_error( "Roles: pravidlo $rule_name > $rule_method neni definovano." );
		}
		return $rule_method == null ? $this->rules[$rule_name] : $this->rules[$rule_name][$rule_method];
	}

	/**
	 * Hodnoti jednotlive prvky podminky s roli.
	 * @param String $rule_name - tema podminky (novinky..)
	 * @param String $rule_method - metoda podminky (editace..)
	 * @param String $role_name - pro jakou roli se ma dana situace hodnotit
	 * @param boolean $is_actual_role - urcuje, jestli jde o rodicovskou roli
	 * nebo o aktualni roli daneho uzivatele.
	 * Diky tomu se rozlisuje "not" a "all" prikaz
	 * @return String - viz comparison konstanty
	 */
	private function _compare_rule_and_role($rule_name, $rule_method, $role_name, $is_actual_role)
	{
		$rule_value = $this->_get_rule( $rule_name, $rule_method );
		$allowed = FALSE;

		if ( !is_array( $rule_value ) )
			$rule_value = array($rule_value);

		foreach ( $rule_value as $value )
		{
			//= Pokud je nastaveno "all" a vyslovne nebude dalsi iteraci dana role 
			//= zakazana, vrati se TRUE a bude dana podminka splnena pro tuto roli
			if ( $value == "all" && $is_actual_role )
				$allowed = TRUE;

			//= Zkouma, jestli je dana role vyslovne zakazana
			if ( strpos( $value, "not::" ) !== FALSE && $is_actual_role )
			{
				$values = explode( "::", $value );

				if ( count( $values ) != 2 )
					show_error( "Role library: Chyba pri hledani podminek. " . $value . ' ma spatne definovanou syntaxi "not::&lt;nazev_role&gt;"' );

				//= Podminka je vyslovne zakazana pro tento typ role
				if ( $values[1] == $role_name )
					return self::COMPARISON_FORBIDDEN;
			} else

			//= Zkouma, jestli je dana role vyslovne povolena
			if ( strpos( $value, "only::" ) !== FALSE && $is_actual_role )
			{
				$values = explode( "::", $value );

				if ( count( $values ) != 2 )
					show_error( "Role library: Chyba pri hledani podminek. " . $value . ' ma spatne definovanou syntaxi "only::&lt;nazev_role&gt;"' );

				//= Podminka je vyslovne povolena pro tento typ role
				if ( $values[1] == $role_name )
					return self::COMPARISON_ALLOWED;
			}
			else
			{
				//= Tato role je v podmince povolena
				if ( $value == $role_name )
					$allowed = TRUE;
			}
		}

		//= Bud je povolena prikazem ALL (a vyslovne nezakazana) nebo pro danou roli podminka urcena.
		return $allowed ? self::COMPARISON_FOUND : self::COMPARISON_NOT_FOUND;
	}

	/**
	 * Vrati vsechny role, ktere k dane roli maji navaznost.<br>
	 * Vysledek se vrati v poli
	 * @return array
	 */
	private function _get_role_dependency_array()
	{
		$my_role_value = $this->_get_role( $this->user_role );

		if ( $my_role_value == self::DEFAULT_ROLE OR
				  $my_role_value == NULL OR
				  $my_role_value == "all"
		)
			return array($this->user_role);

		$roles = array($this->user_role);
		$role = $this->user_role;

		while ( $role != null )
		{

			$role = $this->_get_parent_role( $role );
			if ( $role != null )
				$roles[] = $role;
		}

		return $roles;
	}

	/**
	 * Vrati rodicovskou roli. Pokud zadna neni, vrati null
	 * @param String $role_name
	 * @return null / String 
	 */
	private function _get_parent_role($role_name)
	{

		if ( strpos( $this->_get_role( $role_name ), "from::" ) !== FALSE )
		{
			$values = explode( "::", $this->_get_role( $role_name ) );
			if ( count( $values ) != 2 )
				show_error( "Role library: Chyba pri hledani zavyslosti. " . $role_value . ' ma spatne definovanou syntaxi "from::&lt;nazev_role&gt;"' );

			return $values[1];
		}
		return null;
	}

}

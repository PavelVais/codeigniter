<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

//require_once('phpass-0.1/PasswordHash.php');
/**
 * Popis knihovny User.
 * Jedna se o statickou tridu, ktera se vytvori na zacatku behu systemu
 * a pote je vsude stejna.
 *
 * @author Pavel Vais
 * @todo Vyhodit user_autologin model!
 */
final class User {

	const ACCOUNT_ACTIVATED = 1;
	const ACCOUNT_NOT_ACTIVATED = 0;

	private static $ci;
	private static $user_data;
	public $roles;

	/**
	 * Pokud se zavola ziskani nejake promenne (get_variable())
	 * Tak se jeji hodnota ulozi do cache.
	 * Jakmile se hodnota zmeni pres set_variable() tak se cache invaliduje
	 * @var type 
	 */
	private static $cache;

	/**
	 * Nahrada konstroktoru.
	 * Vola se predtim, pri tvorbe controlleru.
	 */
	final static public function init()
	{
		self::$ci = & get_instance();
		self::$ci->load->library( 'session' );
		self::$ci->load->language( "authorization" );
		self::$ci->load->language( "tank_auth" );
		self::$ci->load->config( 'authorization', TRUE );
		self::$ci->load->config( 'tank_auth' );
		self::$ci->load->helper( 'passwordhash' );

		self::autologin();
	}

	/**
	 * Kontroluje, jestli je uzivatel prihlasen.
	 * Pokud je, a je zaple "secure_authorization", kontorluje se
	 * jestli ma platny token. Pokud vse projde, vraci se TRUE
	 * jinak je FALSE 
	 * @param boolean $activated - ma se zeptat, jestli je zalogovan
	 * a pritom aktivni?
	 * @return boolean
	 */
	final static public function is_logged_in($activated = TRUE)
	{
		$status = self::$ci->session->userdata( 'status' ) === ($activated ? self::ACCOUNT_ACTIVATED : self::ACCOUNT_NOT_ACTIVATED);
		if ( !$status )
			return FALSE;

		if ( $status && self::$ci->config->item( 'secure_authorization', 'authorization' ) )
			if ( self::generate_secure_token() != self::$ci->session->userdata( 'token' ) )
			{
				//FB::info(self::generate_secure_token()." - ".self::$ci->session->userdata( 'token' ),"is_logged_in logout");
				self::logout();
				return FALSE;
			}

		return TRUE;
	}

	/**
	 * Ziska se role uctu.
	 * @return String
	 */
	final static public function get_role()
	{
		self::load_profile_data();
		return self::$user_data['role'];
	}

	final static public function get_profile_data($name)
	{
		self::load_profile_data();
		return isset( self::$user_data[$name] ) ? self::$user_data[$name] : false;
	}

	/**
	 * Zmeni heslo uzivateli. Pokud neni ID zadano, veme se ID aktualniho uzivatele.
	 * Stare heslo nemusi byt zadano, v ten pripad se nekontroluje jestli 
	 * uzivatel opravdu muze zmenit heslo, proste se zmeni.
	 * @param type $new_password - nove heslo
	 * @param type $old_password - stare heslo
	 * @param type $user_id - ID uzivatele (defaultne ID prihlaseneho uzivatele.)
	 * Pokud neni ID zadano a neni nikdo prihlasen, funkce vraci FALSE
	 * @return boolean
	 */
	final static public function change_password($new_password, $old_password = null, $user_id = null)
	{
		if ( $user_id === null )
			$user_id = User::is_logged_in() ? User::get_id() : null;

		if ( $user_id === null )
			return false;

		$user_model = new UsersModel();
		$hasher = new PasswordHash(
			   self::$ci->config->item( 'phpass_hash_strength', 'authorization' ), self::$ci->config->item( 'phpass_hash_portable', 'authorization' ) );

		if ( $old_password !== null )
		{
			return $user_model->change_password_safe( $user_id, $new_password, $hasher->HashPassword( $old_password ) );
		}
		else
		{
			return $user_model->change_password( $user_id, $hasher->HashPassword( $new_password ) ) == FALSE ? FALSE : TRUE;
		}
	}

	/**
	 * 3 steps funkce *******
	 * Recovery password se stara o obnovu hesla. Pokud je zadan POUZE email
	 * tak funkce vrati novy token k vygenerovani.
	 * Pokud jiz token existuje, vraci FALSE.<br>
	 * Pokud je zadan email a token, tak vrati pouze TRUE, paklize se token shoduje
	 * nebo FALSE, pokud ne. Token se nicmene nesmaze, jen se checkuje.
	 * Pokud je zadan i password, token se po spravnem checknuti i vymaze, nasledne
	 * se zmeni uzivateli heslo.
	 * 
	 * @param String $email
	 * @param String $token
	 * @param String $new_password
	 * @return boolean
	 */
	final static public function recovery_password($email, $token = null, $new_password = null)
	{
		$user_model = new UsersModel();
		$user = $user_model->get_user_by_email( $email );

		$cm = new ConfirmModel;
		if ( $token == null )
		{
			$new_token = $cm->add_tag( $user->id )->generate( TRUE, ConfirmModel::EXPIRATION_DAY, "pass_change" );

			if ( $new_token == FALSE && $cm->get_error_code() == 506 )
				return false;
			else
				return $new_token;
		} else
		{

			$result = $cm->check_confirm( $token, false, $new_password == null ? FALSE : TRUE, false, true );

			if ( $result !== FALSE && $new_password !== null )
				return self::change_password( $new_password, null, intval( $result->tags ) );

			return $result === FALSE ? FALSE : TRUE;
		}
	}

	final static public function generate_link_to_password()
	{
		
	}

	/**
	 * Prihlasi uzivatele dle loginu a hesla.
	 * Dle toho, co je nastaveno v nastaveni, se za login povazuje bud
	 * username nebo email (v hlavnich pripadech).
	 * Pokud neco neni v poradku VYHODI se vyjimka. Takze je nutne mit
	 * tuto funkci v try catch bloku!!
	 * @param String $login
	 * @param String $password
	 * @param boolean $autologin
	 * @return boolean - TRUE , vse je ok.
	 * @throws Exception
	 */
	final static public function login($login, $password, $autologin = true)
	{
		//	self::init();
		$user_model = new UsersModel();

		$user = $user_model->login( $login, self::$ci->config->item( 'login_column', 'authorization' ) );

		if ( $user == false || $user->deleted == 1 )
		{
			//= Ucet nebyl nalezen
			throw new Exception( self::$ci->lang->line( 'auth.login.incorrect' ) );
		}

		$hasher = new PasswordHash(
			   self::$ci->config->item( 'phpass_hash_strength', 'authorization' ), self::$ci->config->item( 'phpass_hash_portable', 'authorization' ) );

		if ( strlen( $user->password ) === 0 )
		{
			//= Heslo není vyplněno
			throw new Exception( self::$ci->lang->line( 'auth.password.empty' ) );
		}

		if ( !$hasher->CheckPassword( $password, $user->password ) )
		{
			//= Spatne heslo
			throw new Exception( self::$ci->lang->line( 'auth.password.incorrect' ) );
		}

		if ( $user->banned == 1 )
		{
			//= Ucet je zablokovan
			throw new Exception( self::$ci->lang->line( 'auth.account.banned' ) . " Důvod: " . $user->ban_reason );
		}

		if ( $user->activated == 0 )
		{
			//= Ucet neni autorizovan
			throw new Exception( self::$ci->lang->line( 'auth.account.not_activated' ) );
		}

		//= Finally! ucet se potvrdil
		$session = array(
		    'user_id' => $user->id,
		    'username' => $user->username,
		    'status' => ($user->activated == 1) ? 1 : 0
		);

		//= Vygenerovani zabezpecovaciho tokenu. Pokud se session ukradne, na jinem
		//= PC by nemel session fungovat
		if ( self::$ci->config->item( 'secure_authorization', 'authorization' ) )
			$session['token'] = self::generate_secure_token();


		self::$ci->session->set_userdata( $session );
		FB::info( $autologin, "info var autologin" );
		if ( $autologin )
			self::create_autologin( $user->id );


		return TRUE;
	}

	/**
	 * @todo DODĚLAT
	 * @param type $user_id
	 */
	static public function force_login($user_id, $autologin = false)
	{
		$user_model = new UsersModel();

		$user = $user_model->get_user_by_id( $user_id );

		if ( $user === false )
		{
			//= Ucet nebyl nalezen
			throw new Exception( $ci->lang->line( 'auth.login.incorrect' ) );
		}

		if ( $user->banned == 1 )
		{
			//= Ucet je zablokovan
			throw new Exception( self::$ci->lang->line( 'auth.account.banned' ) . " Důvod: " . $user->ban_reason );
		}

		if ( $user->activated == 0 )
		{
			//= Ucet neni autorizovan
			throw new Exception( self::$ci->lang->line( 'auth.account.not_activated' ) );
		}

		//= Finally! ucet se potvrdil
		//= nejdrive zkusime odlogovat
		self::logout();

		$session = array(
		    'user_id' => $user->id,
		    'username' => $user->username,
		    'status' => ($user->activated == 1) ? 1 : 0
		);

		//= Vygenerovani zabezpecovaciho tokenu. Pokud se session ukradne, na jinem
		//= PC by nemel session fungovat
		if ( self::$ci->config->item( 'secure_authorization', 'authorization' ) )
			$session['token'] = self::generate_secure_token();

		self::$ci->session->set_userdata( $session );
		if ( $autologin )
			self::create_autologin( $user->id );

		return TRUE;
	}

	/**
	 * Logout user from the site
	 *
	 * @return	void
	 */
	final static function logout()
	{
		self::_delete_autologin();

		$session = array(
		    'user_id' => '',
		    'username' => '',
		    'status' => '',
		    'token' => ''
		);
		//FB::info("provadim logout","logout");
		// See http://codeigniter.com/forums/viewreply/662369/ as the reason for the next line
		self::$ci->session->set_userdata( $session );
		self::$ci->session->sess_destroy();
	}

	final static function reload_session($username = null, $status = null)
	{
		//= Finally! ucet se potvrdil
		$session['user_id'] = User::get_id();
		$session['username'] = $username != null ? $username : User::get_username();
		$status['status'] = $status != null ? $status : self::$ci->session->userdata( 'status' );

		//= Vygenerovani zabezpecovaciho tokenu. Pokud se session ukradne, na jinem
		//= PC by nemel session fungovat
		if ( self::$ci->config->item( 'secure_authorization', 'authorization' ) )
			$session['token'] = self::generate_secure_token();


		self::$ci->session->set_userdata( $session );
	}

	/**
	 * Ulozi noveho uzivatele do databaze.
	 * Pokud se nevlozi argument $activated, ucet bude aktivovan, pokud
	 * v nastaveni NENI nastaveno "aktivovat emailem".
	 * V tomto pripade se vlozi neaktivovany uzivatel
	 * @param String $username - uzivatelske jmeno
	 * @param String $email - email k uctu
	 * @param String $password - heslo k uctu
	 * @param int $activated - ma byt ucet aktivovan? (mozno se k nemu prihlasit)
	 * @param String $role
	 * @return int kdyz se uspesne ulozi, vrati se IDcko ulozeneho uctu
	 * jinak se vrati vyjimka!
	 * @throws Exception 
	 */
	final static function register($username, $email, $password, $activated = -1, $role = "registered")
	{
		$user_model = new \UsersModel;
		if ( self::$ci->config->item( 'check_unique_username', 'authorization' ) && !$user_model->is_user_avaible( $username, UsersModel::USERNAME_CHECK ) )
		//= Ucet s danym username jiz existuje
			throw new Exception( self::$ci->lang->line( 'auth.username.in_use' ) );

		if ( self::$ci->config->item( 'check_unique_email', 'authorization' ) && !$user_model->is_user_avaible( $email, UsersModel::EMAIL_CHECK ) )
		//= Ucet s danym emailem jiz existuje
			throw new Exception( self::$ci->lang->line( 'auth.email.in_use' ) );

		$hasher = new PasswordHash(
			   self::$ci->config->item( 'phpass_hash_strength', 'authorization' ), self::$ci->config->item( 'phpass_hash_portable', 'authorization' ) );

		$hashed_password = $hasher->HashPassword( $password );

		//= Pokud neni urceno jinak, tak pokud ucet vyzaduje aktivaci emailem, vlozi se do
		//= db jako neaktivni
		if ( $activated == -1 )
			$activated = (self::$ci->config->item( 'email_activation', 'autorization' )) ? 0 : 1;

		if ( $activated === FALSE )
			$activated = 0;

		$data = array(
		    'username' => $username,
		    'password' => $hashed_password,
		    'email' => $email,
		    'last_ip' => self::$ci->input->ip_address(),
		    'last_login' => DMLHelper::now( TRUE ),
		    'created' => DMLHelper::now( TRUE ),
		    'activated' => $activated,
		    'role' => $role
		);

		if ( ($langColumn = self::$ci->config->item( 'user_language_column', 'authorization' )) != FALSE )
			$data[$langColumn] = self::$ci->lang->lang();


		if ( $user_model->save_user( $data ) !== FALSE )
			return $user_model->last_id();

		throw new Exception( $user_model->get_error_message() );
	}

	/**
	 * Save data for user's autologin
	 * @todo Odstranit zavyslost na tank_authu
	 * @param	int
	 * @return	bool
	 */
	final static public function create_autologin($user_id)
	{
		self::$ci->load->helper( 'cookie' );
		$key = substr( md5( uniqid( rand() . get_cookie( self::$ci->config->item( 'sess_cookie_name' ) ) ) ), 0, 16 );

		$UAL = new \UserAutologinModel;
		$UAL->purge( $user_id );

		if ( $result = $UAL->set( $user_id, md5( $key ) ) )
		{
			set_cookie( array(
			    'name' => self::$ci->config->item( 'autologin_cookie_name', 'authorization' ),
			    'value' => serialize( array('user_id' => $user_id, 'key' => $key) ),
			    'expire' => self::$ci->config->item( 'autologin_cookie_life', 'authorization' ),
			) );
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Login user automatically if he/she provides correct autologin verification
	 * @todo Odstranit zavyslost na tank_authu
	 * @return	void
	 */
	final static function autologin()
	{
		if ( self::is_logged_in() )
			return;

		self::$ci->load->helper( 'cookie' );
		if ( $cookie = get_cookie( self::$ci->config->item( 'autologin_cookie_name', 'authorization' ), TRUE ) )
		{

			$data = unserialize( $cookie );

			if ( isset( $data['key'] ) AND isset( $data['user_id'] ) )
			{

				$userAL = new UserAutologinModel;
				$user = $userAL->get( $data['user_id'], md5( $data['key'] ) );
				if ( $user != false )
				{
					$session = array(
					    'user_id' => $user->id,
					    'username' => $user->username,
					    'status' => self::ACCOUNT_ACTIVATED
					);

					//= Vygenerovani zabezpecovaciho tokenu. Pokud se session ukradne, na jinem
					//= PC by nemel session fungovat
					if ( self::$ci->config->item( 'secure_authorization', 'authorization' ) )
						$session['token'] = self::generate_secure_token();

					// Login user
					self::$ci->session->set_userdata( $session );

					// Renew users cookie to prevent it from expiring
					set_cookie( array(
					    'name' => self::$ci->config->item( 'autologin_cookie_name', 'authorization' ),
					    'value' => $cookie,
					    'expire' => self::$ci->config->item( 'autologin_cookie_life', 'authorization' ),
					) );

					$UM = new \UsersModel;
					$UM->update_login_info( $user->id, self::$ci->config->item( 'login_record_ip', 'tank_auth' ), self::$ci->config->item( 'login_record_time', 'tank_auth' ) );
					return TRUE;
				}
			}
		}
		return FALSE;
	}

	/**
	 * Ziska promennou, ktera se vstahuje k tomuto uctu pomoci 
	 * Variable modelu.
	 * @param String $name
	 * @return String / int / FALSE
	 * - pokud se nic nenajde, vraci se FALSE
	 */
	final static public function get_variable($name)
	{
		if ( ($value = self::getFromCache( $name )) == false )
		{
			$v = new VariablesModel();
			$value = $v->get( $name );
			self::saveToCache($value, $name);
		}
		return $value;
	}

	/**
	 * Ulozi do databaze informaci vstahujici se k danemu uctu.
	 * @param String $name
	 * @param String/int $value
	 * @return boolean
	 */
	final static public function set_variable($name, $value)
	{
		$v = new VariablesModel();
		self::invalideCache( $name );
		return $v->set( $name, $value );
	}

	/**
	 * Smaze informaci vstahujici se k danemu uctu
	 * @param String $name
	 * @return boolean
	 */
	final static public function destroy_variable($name)
	{
		$v = new VariablesModel();
		self::invalideCache( $name );
		return $v->delete( $name );
	}

	/**
	 * Smaze vsechny promenne spojene s danym uctem
	 * @return boolean
	 */
	final static public function purge_variables()
	{
		$v = new VariablesModel();
		self::deleteCache();
		return $v->purge();
	}

	/**
	 * Prejmenuje promenou
	 * @param String $old_name
	 * @param String $new_name
	 * @return boolean
	 */
	final static function rename_variable($old_name, $new_name)
	{
		$v = new VariablesModel();
		self::saveToCache( self::getFromCache( $old_name ), $new_name );
		self::invalideCache( $old_name );
		return $v->rename( $old_name, $new_name );
	}

	/**
	 * Pokud je to vyzadovano, stahne dodatecne data z databaze
	 * a ulozi do staticke promenne $user_data.
	 * @param type $force_reload - data se nactou, ikdyz uz jednou 
	 * nacteny jsou
	 * @return boolean
	 */
	final static private function load_profile_data($force_reload = false)
	{
		if ( self::_is_user_profile_data_loaded() && !$force_reload )
			return TRUE;

		$users = new UsersModel();

		$data = $users->get_user_by_id( self::get_id() );

		if ( $data === FALSE )
		{
			//= Neco se stalo, nelze se podivat na detail uctu
			//= Radsi provedu logout
			self::logout();
			return false;
		}

		self::$user_data = get_object_vars( $data );
	}

	/**
	 * Vrati IDcko prave vyuzivaneho uctu.
	 * Tato funkce by se nemela pouzivat pro kontrolu, jestli je
	 * uzivatel prihlasen!!
	 * @return int / false
	 */
	final static public function get_id()
	{
		if ( ($id = self::getFromCache( 'id', 'COMMON' )) == false )
		{
			$id = (int) self::$ci->session->userdata( 'user_id' );
			self::saveToCache( $id, 'id', 'COMMON' );
		}
		return $id;
	}

	/**
	 * Vraci jmeno uctu, ktery je prave prihlasen
	 * @return String / false
	 */
	final static public function get_username()
	{
		return self::$ci->session->userdata( 'username' );
	}

	/**
	 * Vygeneruje unikatni token, vstahujici se k danemu pocitaci.
	 * Zabezpecuje ukradnuti session a pouziti na jinem pocitaci.
	 * @return String
	 */
	public static function generate_secure_token()
	{
		self::$ci->load->library( 'user_agent' );
		$token = "";

		if ( self::$ci->agent->is_browser() )
		{
			$token .= self::$ci->agent->browser() . ' ' . self::$ci->agent->version();
		}

		$token .= self::$ci->input->ip_address();
		$token = str_replace( array('.', ' '), '', $token );
		//= Prelozeni tokenu na cislo
		$hash = hash( "sha256", $token );

		return $hash;
	}

	/**
	 * Kontrolni funkce, ktera zjisti, jestli jsou nactena
	 * dodatecna data pro dany ucet
	 * @return type
	 */
	static private function _is_user_profile_data_loaded()
	{
		return is_array( self::$user_data ) ? TRUE : FALSE;
	}

	/**
	 * Clear user's autologin data
	 * @todo Odstranit zavyslost na tank_authu
	 * @return	void
	 */
	static private function _delete_autologin()
	{
		self::$ci->load->helper( 'cookie' );
		if ( $cookie = get_cookie( 'autologin', TRUE ) )
		{

			$data = unserialize( $cookie );

			$UAL = new \UserAutologinModel;
			$UAL->delete( $data['user_id'], md5( $data['key'] ) );

			delete_cookie( 'autologin' );
		}
	}

	public static function __generate_password($password)
	{
		$hasher = new PasswordHash(
			   self::$ci->config->item( 'phpass_hash_strength', 'authorization' ), self::$ci->config->item( 'phpass_hash_portable', 'authorization' ) );

		return $hasher->HashPassword( $password );
	}

	/**
	 * Vymaze celou docasnou cache.
	 */
	static public function deleteCache()
	{
		self::$cache = null;
	}

	/**
	 * Invaliduje se cast cache, aby nebyla znova volana
	 * @param String $name - Nazev cache ktera se vymaze
	 * @param String $type - VARIABLES , COMMON (id, atp)
	 */
	private static function invalideCache($name, $type = "VARIABLES")
	{
		if ( isset( self::$cache[$type][$name] ) )
			unset( self::$cache[$type][$name] );
	}

	/**
	 * 
	 * @param type $name - nazev cache
	 * @param type $type - typ cache (VARIABLES, COMMON)
	 * @return mixed, pokud cache neexistuje, vrati se FALSE
	 */
	private static function getFromCache($name, $type = "VARIABLES")
	{
		if ( isset( self::$cache[$type][$name] ) )
			return self::$cache[$type][$name];
		else
			return false;
	}

	/**
	 * Ulozi do cache pozadovanou hodnotu
	 * @param type $name - nazev cache
	 * @param type $type - typ cache (VARIABLES, COMMON)
	 * @return mixed, pokud cache neexistuje, vrati se FALSE
	 */
	private static function saveToCache($value, $name, $type = "VARIABLES")
	{
		self::$cache[$type][$name] = $value;
	}

}

?>

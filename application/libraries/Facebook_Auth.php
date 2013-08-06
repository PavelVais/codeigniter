<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author Daw
 */
final class Facebook_Auth
{

	private static $user_data;
	private static $ci;

	/**
	 * Tato promenna urcuje, jestli je tato trida autorizovana
	 * nebo neni.
	 * Autorizovana je pouze kdyz se uzivatel prihlasuje.
	 * Po prihlaseni se berou informace ze session a tato trida
	 * se povazuje za neautorizovanou!
	 * @var boolean
	 */
	private static $authorized = FALSE;

	public static function init()
	{
		self::$ci = & get_instance();
		self::$ci->load->library( 'session' );
		self::$ci->load->language( "tank_auth" );
		self::$ci->load->config( 'authorization', TRUE );
	}

	/**
	 * Vrati ulozene idcko facebooku.
	 * Doporucuje se ale pouzivat funkce User::get_profile_data('fcb_id');
	 * Tato funkce obsahuje IDcko pouze pokud probehne autorizace a prihlaseni.
	 * Tudiz jen pri loginu uzivatele. Pokud uzivatel JE jiz prihlasen,
	 * tato trida je NEAUTORIZOVANA a neobsahuje zadna data
	 * @return int
	 */
	final static public function get_id()
	{
		return isset( self::$user_data['user_id'] ) ? self::$user_data['user_id'] : FALSE;
	}

	/**
	 * Vrati data, ktere facebook vraci v signed_requestu.
	 * Tato funkce obsahuje data pouze pokud probehne autorizace a prihlaseni.
	 * Tudiz jen pri loginu uzivatele. Pokud uzivatel JE jiz prihlasen,
	 * tato trida je NEAUTORIZOVANA a neobsahuje zadna data
	 * @return String
	 */
	final static public function get_data($name)
	{
		return isset( self::$user_data[$name] ) ? self::$user_data[$name] : FALSE;
	}

	final static public function is_profile_exists()
	{
		if ( !isset( self::$user_data['is_profile_exists'] ) )
		{
			self::get_profile( self::get_id() );
		}
		return self::$user_data['is_profile_exists'];
	}

	final static public function get_profile($facebook_id)
	{
		if ( !isset( self::$user_data['facebook_profile'] ) )
		{
			$user_model = new UsersModel;
			self::$user_data['facebook_profile'] = $user_model->get_user_by_facebook_id( $facebook_id );
			self::$user_data['is_profile_exists'] = self::$user_data['facebook_profile'] === FALSE ? FALSE : TRUE;
		}
		return self::$user_data['facebook_profile'];
	}

	/**
	 * Nalogovani se pomoci facebooku.
	 * Funkce kontroluje, jestli je ucet v databazi, pokud neni, zalozi se novy.
	 * Pokud se neco nepovede, funkce vyhodi vyjimku.
	 * @param String $username - Jmeno uzivatele, ktere vratil facebook
	 * @param String $role [optional] - role, ktera bude uctu prirazena, paklize
	 * se bude nove zakladat.
	 * @return int - Pokud funkce vrati jednicku, uzivatel se nalogoval do jiz
	 * existujiciho uctu. Pokud vraci dvojku, uzivatel se nalogoval do NOVEHO
	 * uctu.
	 * @throws Exception 
	 */
	final static public function login_and_register($username, $role = "registered")
	{

		if ( !self::$authorized )
		{
			throw new Exception( self::$ci->lang->line( 'auth_fcb_cant_log_in' ) );
		}

		$user_model = new UsersModel;
		if ( ($result = self::get_profile( self::get_id() )) === FALSE  )
		{
			//= Ucet s danym facebook idckem v db neexistuje,
			//= Zkusime ho vytvorit
			//= Zjistime vlozene ID

			$data = array(
				 'username' => $username,
				 'fcb_id' => self::get_id(),
				 'role' => $role,
				 'last_ip' => self::$ci->input->ip_address(),
				 'last_login' => DMLHelper::now( TRUE ),
				 'created' => DMLHelper::now( TRUE ),
				 'activated' => 1
			);

			$cresult = $user_model->save_user( $data );

			$last_id = $user_model->last_id();

			if ( $cresult == FALSE )
				throw new Exception( $user_model->get_error_message() );

			//= Vypada to dobre, nyni se prihlasime
			self::create_session( $last_id, $username );

			return 2;
		}
		else
		{
			//= otestovani, jestli je uzivtel radne prihlasen
			//= Hlavne co se tyce prihlaseni pres facebook, tak to zlobi
			if ( strlen( $username ) == 1 )
			{
				throw new Exception( self::$ci->lang->line( 'auth_generic_error' ) );
			}
			//= Ucet s danym facebook idckem je jiz zaregistrovan.
			//= Zkusime se prihlasit
			self::create_session( $result->id, $result->username );
			return 1;
		}
	}

	/**
	 * Registrovani informaci do sessionu a tim padem
	 * nalogovani uzivatele do systemu.
	 * @param int $id - ID uctu, ne facebook id!!
	 * @param String $username
	 */
	private static function create_session($id, $username)
	{
		$session = array(
			 'user_id' => $id,
			 'username' => $username,
			 'status' => 1
		);


		if ( self::$ci->config->item( 'secure_authorization', 'authorization' ) )
		//= Vygenerovani zabezpecovaciho tokenu. Pokud se session ukradne, na jinem
		//= PC by nemel session fungovat
			$session['token'] = User::generate_secure_token();

		self::$ci->session->set_userdata( $session );
	}

	/**
	 * Autorizacni funkce facebooku.
	 * Facebook vrati klic, ve kterem zakodoval IDcko uzivatele a token,
	 * ktery tato funkce rozlusti pomoci tajneho klice aplikace
	 * (ten se definuje v config/authorization.php)
	 * Pokud vse probehne v poradku, tato staticka trida
	 * se zmeni v autorizovanou tridu a clovek se muze prihlasit.
	 * @see https://developers.facebook.com/docs/howtos/login/signed-request/
	 * @param String $signed_request - autorizacni token, ktery vraci
	 * api facebooku
	 * @return TRUE - vse se povedlo, pokud se neco nezdari,
	 * funkce vyhodi vyjimku.
	 * @throws Exception
	 */
	public static function authorization($signed_request)
	{
		self::$authorized == FALSE;
		$secret = self::$ci->config->item( 'fcb_secred_app_id', 'authorization' );

		if ( strpos( $signed_request, "." ) === FALSE )
		{
			Logs::warning( "Pokus o nalogování přes Facebook se špatným tokenem.", null, "facebook login" );
			throw new Exception( self::$ci->lang->line( 'auth_fcb_cant_log_in' ) );
		}
		list($encoded_sig, $payload) = explode( '.', $signed_request, 2 );

		// decode the data
		$decode = function($input)
				  {
					  return base64_decode( strtr( $input, '-_', '+/' ) );
				  };

		$sig = $decode( $encoded_sig );
		$data = json_decode( $decode( $payload ), true );
		if ( strtoupper( $data['algorithm'] ) !== 'HMAC-SHA256' )
		{
			Logs::warning( "Pokus o nalogování přes Facebook se špatným tokenem.", null, "facebook login" );
			throw new Exception( self::$ci->lang->line( 'auth_fcb_cant_log_in' ) );
		}

		// Adding the verification of the signed_request below
		$expected_sig = hash_hmac( 'sha256', $payload, $secret, $raw = true );
		if ( $sig !== $expected_sig )
		{
			Logs::warning( "Pokus o nalogování přes Facebook se špatným tokenem.", null, "facebook login" );
			throw new Exception( self::$ci->lang->line( 'auth_fcb_cant_log_in' ) );
		}
		self::$user_data = $data;
		self::$authorized = TRUE;
		return TRUE;
	}

}

?>

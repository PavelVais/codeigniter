<?php

class Secure {

	private static $ci;

	/**
	 * Nazev pojmenovani CSRF tokenu v GET a POST situacich 
	 */

	const CSRF_TOKEN_NAME = "secutoken";

	/**
	 * Urcuje vygenerovany token
	 * @var type 
	 */
	private static $generated_token;

	/**
	 * Urcuje, jestli je trida jiz zinicializovana nebo ne
	 * @var type 
	 */
	private static $loaded;

	final static public function init()
	{
		if ( self::$loaded === TRUE )
			return false;

		self::$ci = & get_instance();
		self::$ci->load->library( 'session' );
		//self::$ci->load->library('user_agent');
		self::$ci->load->helper( 'form' );
		self::$generated_token = null;
		self::$loaded = true;
	}

	/**
	 * Vygenerovani SESSION, do ktereho se vlozi
	 * nove vygenerovany token. Slouzi proti CSRF utoku
	 */
	final static public function csrf_session_generate()
	{
		self::init();
		$session[self::CSRF_TOKEN_NAME] = self::get_token();
		self::$ci->session->set_userdata( $session );
	}

	/**
	 * Vlozi hidden formular, do ktereho vypise token,
	 * ktery musite pote na dalsi strance validovat pomoci
	 * secure::csrf_check()
	 */
	final static public function csrf_post()
	{
		self::init();
		self::csrf_session_generate();
		echo form_hidden( self::CSRF_TOKEN_NAME, self::get_token() );
	}

	/**
	 * Obdobna funkce jako csrf_anchor funkce, nicmene nevraci anchor,
	 * ale samotny token
	 * @param boolean $uri_to_assoc - ma se vlozit "secutoken/token" (true) nebo jen "token" ?
	 * @param boolean $leading_slash - ma zacinat lomitkem?
	 * @return String
	 */
	final static public function csrf_get($uri_to_assoc = FALSE, $leading_slash = TRUE)
	{
		self::init();
		self::csrf_session_generate();
		$leading_slash = $leading_slash ? "/" : "";
		return $uri_to_assoc ? $leading_slash . self::CSRF_TOKEN_NAME . "/" . self::get_token() : $leading_slash . self::get_token();
	}

	/**
	 * Funkce, ktera na konci url dosadi csrf token
	 * @param type $url
	 * @param boolean $uri_to_assoc - ma se vlozit "secutoken/token" (true) nebo jen "token" ?
	 * @return type
	 */
	final static public function csrf_url($url, $uri_to_assoc = FALSE)
	{
		self::init();
		self::csrf_session_generate();
		$url = rtrim( $url, "/" );
		return base_url( $url . "/" . ($uri_to_assoc ? self::CSRF_TOKEN_NAME . "/" . self::get_token() : self::get_token()) );
	}

	/**
	 * Cross-site request forgery obrana vlozena do linku.
	 * Tato funkce zabezpeci odkaz pomoci CSRF tokenu, ktery se musi
	 * pote na dane strance zvalidovat funkci "csrf_check"
	 * Tatno funkce funguje stejne jako normalni "anchor" funkce
	 * @param type $url - URL adresa
	 * @param type $name - nazev linku
	 * @param type $url_to_assoc - ma se vlozit "secutoken/......" (true)
	 * nebo samotny token (false) ? [false]
	 * @param type $extra - do array listu vlozit ostatni udaje, ktere chcete v
	 * anchoru vykreslit. Tyto extra udaje muzete jeste vyfiltrovat pomoci $extra_xss_filter
	 * @param $extra_xss_filter - provede se filtrace vlozenych extra hodnot (true)
	 * @return string
	 */
	final static public function csrf_anchor($url, $name = null, $extra = null, $uri_to_assoc = false, $extra_xss_filter = false)
	{
		self::init();
		self::csrf_session_generate();
		$url = rtrim( $url, "/" );

		if ( $extra_xss_filter && is_array( $extra ) )
			foreach ( $extra as &$value )
				$value = self::xss_html( $value );

		return anchor( $url . "/" . ($uri_to_assoc ? self::CSRF_TOKEN_NAME . "/" . self::get_token() : self::get_token()), $name, $extra );
	}

	/**
	 * Funkce na validaci csrf utoku.<br>
	 * Dokaze automaticky validovat jak POST tak GET data, popripade
	 * validovat primo vlozeny token.
	 * @param type $token - pokud obsahuje cislo od 3 do 9,
	 * tento parametr se povazuje za index url segmentu,
	 * z ktereho se ma token vzit.<br>
	 * Pokud je token prazdny, zkouma se $_POST['SECUTOKEN'].<br>
	 * V ostatnich pripadech se testuje token, vlozen v promenny
	 * $token
	 * @return boolean - TRUE - validace je OK, FALSE - validace neprosla
	 */
	final static public function csrf_check($token = null)
	{
		self::init();

		if ( $token == null )
			$token = self::$ci->input->post( self::CSRF_TOKEN_NAME );
		else if ( is_numeric( $token ) && $token <= 9 && $token > 2 )
			$token = self::$ci->uri->segment( $token );

		if ( !self::$ci->session->userdata(self::CSRF_TOKEN_NAME))
			return false;

		return self::$ci->session->userdata(self::CSRF_TOKEN_NAME) === $token;
	}

	/**
	 * Ziska se vygenerovany token. Za celou zivotnost volani frameworku
	 * se muze vygenerovat pouze jeden token. ostatni tokeny jsou jiz
	 * stejny.
	 * @return String
	 */
	public static function get_token()
	{
		self::init();
		self::$generated_token = self::$ci->session->userdata(self::CSRF_TOKEN_NAME);
		if ( !self::$generated_token )
		{
			//= Pokud neni token ulozen v session (nebo je expired)
			//= vygeneruje se novy
			self::$generated_token = self::generate_token();
		}
		return self::$generated_token;
	}

	/**
	 * Obrana proti cross-site scripting utoku.
	 * @param String/array $string
	 * @return String
	 */
	public static function xss_html($string)
	{
		if ( !is_array( $string ) )
			$string = array($string);

		foreach ( $string as &$s )
		{
			$s = remove_invisible_characters( $s, "UTF-8" );
			$s = htmlspecialchars( $s, ENT_QUOTES, 'UTF-8' );
		}
		return count( $string ) == 1 ? $string[0] : $string;
	}

	/**
	 * Vygenerovani unikatniho tokenu
	 * @return type
	 */
	private static function generate_token()
	{
		return substr( hash_hmac( 'ripemd160', mt_rand( 1000, 1000000 ) . self::$ci->input->user_agent() . self::$ci->input->ip_address(), "wfewf58e4f8w64wea65f1we5afwe3a313" ), 0, 15 );
	}

}
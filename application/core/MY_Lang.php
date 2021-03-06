<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * Codeigniter i18n library by Jérôme Jaglale
 * updated by Pavel Vais
 *
 */
class MY_Lang extends CI_Lang {
	/*	 * ************************************************
	  configuration
	 * ************************************************* */

	// languages
	var $languages = array(
	    'cs' => 'czech',
	    'en' => 'english',
	    'ru' => 'russian',
	    'de' => 'deutch',
	    'fr' => 'french',
	);
	// special URIs (not localized)
	var $special = array(
	    "administrace"
	);
	// where to redirect if no language in URI
	var $default_uri = '';

	/**
	 * Pokud neni urcena, default language bude vzdy PRVNI 
	 * v poli languages
	 */
	private $default_language = '';

	/*	 * *********************************************** */

	function __construct() {
		parent::__construct();

		global $CFG;
		global $URI;
		global $RTR;

		$segment = isset( $URI->osegments[0] ) ? $URI->osegments[0] : $URI->segment( 1 );
		if ( $segment == $this->default_lang() ) {
			header( "Location: " . $CFG->site_url( str_replace( $this->default_lang(), '', $URI->uri_string() ) ), TRUE, 302 );
			exit;
		}

		if ( strpos( $_SERVER['HTTP_HOST'], "wisheer.com" ) !== false ) {
			$this->set_default_lang( 'en' );
		}

		$this->prepare_segments();

		if ( isset( $this->languages[$segment] ) ) { // URI with language -> ok
			$language = $this->languages[$segment];
			$CFG->set_item( 'language', $language );
		}
		else if ( $this->is_special( $segment ) ) { // special URI -> no redirect
			return;
		}
		else { // URI without language -> redirect to default_uri
			// set default language
			$CFG->set_item( 'language', $this->languages[$this->default_lang()] );

			// redirect
			//header("Location: " . $CFG->site_url($this->localized($this->default_uri)), TRUE, 302);
			return;
		}
	}

	function prepare_segments() {
		global $URI;
		$keys = array_keys( $this->languages );

		foreach ( $keys as $key )
		{
			// Zacina URI nejaky z jazykovych znacek?
			$pattern = '#^' . $key . '(/)?#';
			if ( preg_match( $pattern, $URI->uri_string ) ) {
				// Zameni jen prvni text ktery najde
				$URI->uri_string = preg_replace('/'.$key.'/', '', $URI->uri_string, 1);
				$URI->uri_string = ltrim( $URI->uri_string, '/' );
				break;
			}
		}
		$URI->segments = array();
		$URI->_explode_segments();
		$URI->_reindex_segments();
	}

	// get current language
	// ex: return 'en' if language in CI config is 'english' 
	function lang() {
		global $CFG;
		$language = $CFG->item( 'language' );

		$lang = array_search( $language, $this->languages );
		if ( $lang ) {
			return $lang;
		}

		return NULL; // this should not happen
	}

	public function set_default_lang($string) {
		$this->default_language = $string;
	}

	/**
	 * Prida "th" , "rd" a "st" za cislovku
	 * @param type $number
	 * @return string
	 */
	public function languageNumberPrefix($number) {
		if ( $this->lang() == 'en' )
			return $number >= 3 ? 'th' : ($number == 2 ? 'rd' : 'st');
		else
			return '';
	}

	/**
	 * Fetch a single line of text from the language array. Takes variable number
	 * of arguments and supports wildcards in the form of '%1', '%2', etc.
	 * Overloaded function.
	 *
	 * @access public
	 * @return mixed false if not found or the language string
	 */
	public function line() {
		//get the arguments passed to the function
		$args = func_get_args();

		//count the number of arguments
		$c = count( $args );

		//if one or more arguments, perform the necessary processing
		if ( $c ) {
			//first argument should be the actual language line key
			//so remove it from the array (pop from front)
			$line = array_shift( $args );

			//check to make sure the key is valid and load the line
			$line = ($line == '' OR !isset( $this->language[$line] )) ? $line : $this->language[$line];

			//if the line exists and more function arguments remain
			//perform wildcard replacements
			if ( $line && $args ) {
				$i = 1;
				foreach ( $args as $arg )
				{
					$line = preg_replace( '/\%' . $i . '/', $arg, $line );
					$i++;
				}
			}
		}


		return $line;
	}

	/**
	 * Nacte se view dle aktualniho jazyka. View musi mit postfix dle 
	 * daneho jazykoveho identifikatoru (napr.: 'view_index_cs')
	 * @param String $file
	 * @param array $data
	 * @param boolean $return_as_string
	 * @return String
	 */
	function view($file, $data = '', $return_as_string = false, $lang = null) {
		$CI = & get_instance();
		$name = $file . "_" . $lang == null ? $this->lang() : $lang;
		if ( !file_exists( 'application/views/' . $name . '.php' ) ) {
			$name = $file . "_" . $this->default_lang();
			if ( !file_exists( 'application/views/' . $name . '.php' ) )
				$name = $file;
		}

		return $CI->load->view( $name, $data, $return_as_string );
	}

	/**
	 * Vrati cestu k souboru s priponou daneho jazykoveho identifikatoru.
	 * Pokud soubor s danym postfixem neexistuje, zjisti se, jeslti existuje
	 * soubor s defaultnim jazykovym postfixem. V dalsim pripade se vrati pouze 
	 * samotna url bez postfixu.
	 * priklad: "images/sprites.png" => "images/sprites_cs.png"
	 * @param String $path - cesta k souboru.
	 * @param boolean $return_as_url - vrati se bud absolutni url nebo jen 
	 * relativni.
	 * @return String
	 */
	function link($path, $return_as_url = true) {
		$path_parts = pathinfo( $path );

		$filename = $path_parts['dirname'] . '/' . $path_parts['filename'] . '_' . $this->lang() . '.' . $path_parts['extension'];

		if ( !file_exists( $filename ) ) {
			$filename = $path_parts['dirname'] . '/' . $path_parts['filename'] . '_' . $this->default_lang() . '.' . $path_parts['extension'];
			if ( !file_exists( $filename ) )
				$filename = $path;
		}

		if ( $return_as_url )
			return base_url( $filename );
		else
			return $filename;
	}

	function is_special($uri) {
		$exploded = explode( '/', $uri );
		if ( in_array( $exploded[0], $this->special ) ) {
			return TRUE;
		}
		if ( isset( $this->languages[$uri] ) ) {
			return TRUE;
		}
		return FALSE;
	}

	function switch_uri($lang) {
		$CI = & get_instance();

		$uri = $CI->uri->uri_string();
		return $lang . "/" . $uri;
	}

	// is there a language segment in this $uri?
	function has_language($uri) {
		$first_segment = NULL;

		$exploded = explode( '/', $uri );
		if ( isset( $exploded[0] ) ) {
			if ( $exploded[0] != '' ) {
				$first_segment = $exploded[0];
			}
			else if ( isset( $exploded[1] ) && $exploded[1] != '' ) {
				$first_segment = $exploded[1];
			}
		}

		if ( $first_segment != NULL ) {
			return isset( $this->languages[$first_segment] );
		}

		return FALSE;
	}

	// default language: first element of $this->languages
	function default_lang() {
		if ( $this->default_language != '' )
			return $this->default_language;
		foreach ( $this->languages as $lang => $language )
		{
			return $lang;
		}
	}

	// add language segment to $uri (if appropriate)
	function localized($uri) {
		if ( $this->lang() === $this->default_lang() ) {
			return $uri;
		}

		if ( $this->has_language( $uri ) || $this->is_special( $uri ) || preg_match( '/(.+)\.[a-zA-Z0-9]{2,4}$/', $uri ) ) {
			// we don't need a language segment because:
			// - there's already one or
			// - it's a special uri (set in $special) or
			// - that's a link to a file
		}
		else {
			$uri = $this->lang() . '/' . $uri;
		}

		return $uri;
	}

}

/* End of file */

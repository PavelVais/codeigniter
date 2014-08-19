<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * Controller designed to be for static pages, every static
 * page should be redirected here
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property My_Output $output
 * @property CI_URI $uri
 * @property CI_DB_active_record $db
 */
class Pages extends My_Controller {

	/**
	 * Options
	 * ******************* */
	const useLangFile = false;
	const viewFolder = 'pages';

	private $data = array();

	function __construct() {
		parent::__construct();
	}

	function index($view, $subview = '') {
		Head\Head2::addMeta( array('name' => 'author', 'content' => 'Pavel Vais') );
		$js->deferred = true;

		$desiredView = $this->dashesToCamelCase( $view );
		$desiredView = "view_" . $view . ($subview == '' ? '' : '_' . $subview);
		$potencialCallback = $this->dashesToCamelCase( $view, true );

		if ( !file_exists( 'application/views/'.self::viewFolder.'/' . $desiredView . '.php' ) )
			show_404();

		if ( self::useLangFile ) {
			$this->lang->load( strtolower( $potencialCallback ) );
		}

		if ( method_exists( $this, 'page' . $potencialCallback ) )
			$this->{'page' . $potencialCallback}( $subview );


		$data['title'] = ucfirst( $view ); // Capitalize the first letter
		$data = array_merge( $data, $this->data );
		$this->load->view( "page/view_" . $view . ($subview == null ? '' : '_' . $subview), $this->data );
	}

	/**
	 * 
	 * @param type $image
	 */
	private function setHeader($image) {
		if ( self::useLangFile ) {
			$title = lang( 'title' );
			$description = lang( 'description' );
			$keywords = lang( 'keywords' );
			$this->data['title'] = $title;
		}
		else {
			$title = $this->data( 'title' );
			$description = $this->data( 'description' );
			$keywords = $this->data( 'keywords' );
		}
		Head\Head2::facebook_metas( $title, $image, $description );
		Head\Head2::addMeta( array('name' => 'description', 'content' => $description) );
		Head\Head2::addMeta( array('name' => 'keywords', 'content' => $keywords) );
	}

	/**
	 * @param string $someString
	 * @param type $capitalizeFirstCharacter
	 * @return type
	 */
	private function dashesToCamelCase($someString, $capitalizeFirstCharacter = false) {

		$str = str_replace( ' ', '', ucwords( str_replace( '-', ' ', $someString ) ) );

		if ( !$capitalizeFirstCharacter ) {
			$str[0] = strtolower( $str[0] );
		}

		return $str;
	}

}

/* End of file pages.php */
/* Location: ./application/controllers/pages.php */
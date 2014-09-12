<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_URI $uri
 * @property CI_DB_active_record $db
 * @property Header $header
 * @property Menu $menu
 * @property Tank_auth $tank_auth //sprava prihlasenych
 * @property Template $template
 * @property Message $message
 * @property MY_Lang $lang
 * @property GoogleAnalytics $googleanalytics
 */
class Loader extends My_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->helper( 'text' );
	}

	/**
	 *
	 */
	public function index()
	{
		\Head\Head2::addCSS('pava.loader.css');
		\Head\Head2::addJS('effects.js')->deferred = true;
		\Head\Head2::addJS('pava.loader.js')->deferred = true;
		$this->load->view( 'dev/view_loader');
	}
	
	

}

/* End of file dev/loader.php */
/* Location: ./application/controllers/dev/loader.php */
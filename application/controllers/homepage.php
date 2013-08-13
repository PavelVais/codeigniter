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
class Homepage extends My_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->helper( 'text' );
	}

	public function index()
	{
		$data = array();
		$this->load->view( 'homepage/view_index', $data );
	}
	
	public function we_are_working_on_it()
	{
		$this->lang->view('homepage/view_maintenance');
	}
}

/* End of file homepage.php */
/* Location: ./application/controllers/homepage.php */
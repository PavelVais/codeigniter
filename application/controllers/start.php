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
class Start extends My_Controller
{

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
		$data = array();
		$this->load->view( 'view_start', $data );
	}

	public function database()
	{
		$TM = new TestModel;
		
		Dump($TM->get_all());
		//$this->lang->view( 'homepage/view_maintenance' );
		//FB::info($TM->get_all(),'return');
	}

}

/* End of file homepage.php */
/* Location: ./application/controllers/homepage.php */
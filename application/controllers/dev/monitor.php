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
class Monitor extends My_Controller {

	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		Head::add()->css('dev/jsmonitor.css');
		$this->load->view('dev/view_jsmonitor');
	}

	

}

/* End of file homepage.php */
/* Location: ./application/controllers/homepage.php */
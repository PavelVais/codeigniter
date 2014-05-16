<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * @property GoogleAnalytics $googleanalytics
 */
class GA extends My_Controller {

	
	public function index()
	{
		$data['aa'] = '';
		$this->load->view( 'dev/view_ga', $data );
	}
	

}

/* End of file homepage.php */
/* Location: ./application/controllers/homepage.php */
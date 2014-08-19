<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * @property GoogleAnalytics $googleanalytics
 */
class _head extends My_Controller {

	
	public function index()
	{
		$data['aa'] = '';
		\Head\Head2::init();
		//\Head\Head2::productionRefresh();
		$this->load->view( 'dev/dev_head', $data );
	}
	

}

/* End of file homepage.php */
/* Location: ./application/controllers/homepage.php */
<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * Administracni rozcestnik
 * @author Pavel Vais
 * @property CI_Loader $load
 * @property CI_Form_validation $form_validation
 * @property CI_Input $input
 * @property CI_Email $email
 * @property CI_URI $uri
 * @property CI_DB_active_record $db
 * @property Header $header
 * @property Menu $menu
 * @property Tank_auth $tank_auth
 * @property DbBackup $dbbackup
 * @property Roles $roles
 * @property Cache $cache
 */
class Index extends My_Controller
{

	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$data = array();
		/*$gapi = new gapi('vaispavel@gmail.com', 'b3d2d1f3g5g6d2');
		$gapi->requestReportData('67920161',array('browser','browserVersion'),array('pageviews','visits'));
		
		\FB::info($gapi->getResults(),'$gapi->getResults()');*/
		$this->load->view( 'administrace/view_dashboard', $data );
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
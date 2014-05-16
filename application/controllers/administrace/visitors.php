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
class Visitors extends My_Controller
{

	private $data;

	function __construct()
	{
		parent::__construct();
		$this->navigator->add( 'administrace/visitors/list', 'Statistika přístupů', 'fa-bar-chart-o' );
		
		$this->data['dateFrom'] = new DateTime( 'NOW -1 month' );
		$this->data['dateFrom'] = $this->data['dateFrom']->format( 'd.m.Y' );
		$this->data['dateTo'] = new DateTime( 'NOW' );
		$this->data['dateTo'] = $this->data['dateTo']->format( 'd.m.Y' );
		$this->data['dateString'] = $this->data['dateFrom'] . ' - ' . $this->data['dateTo'];
	}

	public function index()
	{
		$data = array();
		//$GA = new gapiWrapper('67920161'); //(rezivo)
		$GA = new gapiWrapper( '69336713' ); //(jandik)
		$GA->dimension( 'date' )->metric( array('users', 'newUsers', 'avgSessionDuration') );
		$this->data['stat_result'] = $GA->get();
		$GA->dimension( 'browser' )->metric( array('users') )->sort( '-users' )->filter('users > 1');
		$this->data['browser_result'] = $GA->get();
		
		$GA->dimension( 'source' )->metric( array('users') )->sort( '-users' );
		$this->data['source_result'] = $GA->get();



		//\FB::info($data['stat_result'],'a');
		/* $gapi = new gapi('vaispavel@gmail.com', 'b3d2d1f3g5g6d2');
		  $gapi->requestReportData('67920161',array('browser','browserVersion'),array('pageviews','visits'));

		  \FB::info($gapi->getResults(),'$gapi->getResults()'); */
		$this->load->view( 'administrace/visitors/view_stats', $this->data );
	}

	public function getVisitorSource()
	{
		
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
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
		$this->load->library( 'message' );
	}

	public function index()
	{
		$cm = new ConfessionModel();
		$hm = new HashtagsModel();
		$data['count_confessions'] = $cm->count();
		$data['confessions'] = $cm->get_new();
		$data['hashtags'] = $hm->get();
		$data['count_hashtags'] = $hm->count();
		$this->load->view( 'administrace/view_dashboard', $data );
	}

	/**
	 * Obnovi zacachovany data na hlavni strance
	 */
	public function renew_dashboard_data()
	{
		$this->cache->delete( "administrace_dashboard_data" );
		redirect("administrace");
	}
		
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
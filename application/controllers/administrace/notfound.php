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
class NotFound extends My_Controller
{

	private $data;

	function __construct()
	{
		parent::__construct();
		
	}
	
	function index()
	{
		$this->load->view('administrace/view_404');
	}


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
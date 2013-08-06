<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * @property CI_Loader $load
 * @property CI_Form_validation $form_validation
 * @property CI_Input $input
 * @property CI_Email $email
 * @property Tank_auth $tank_auth
 * @property CI_URI $uri
 * @property CI_DB_active_record $db
 * @property Header $header
 * @property Message $message
 * @property Roles $roles
 * @property DbBackup $dbbackup
 */
class Databaze extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->helper( array('form', 'url') );
		$this->load->library( 'form_validation' );
		$this->load->library( 'security' );
		$this->load->library( 'message' );
		$this->load->library( 'roles' );
		$this->lang->load( 'tank_auth' );
	}

	
	function index()
	{
		
	}
	
	function backup()
	{
		$this->dbbackup->backup($savename_after_backup, $add_drop_table, $add_insert_data);
	}


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
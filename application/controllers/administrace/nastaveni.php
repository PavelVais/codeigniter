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
class Nastaveni extends My_Controller
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
		$data['confessions_new'] = $cm->get_new();
		$data['confessions_old'] = $cm->get_old( 10 );
		$data['confessions_deleted'] = $cm->get_deleted( 5 );
		$data['hashtags'] = $hm->get();
		$data['count_hashtags'] = $hm->count();
		$data['form'] = $this->_create_fcb_form();
		$this->load->view( 'administrace/nastaveni/view_index', $data );
	}

	private function _create_fcb_form()
	{
		$vm = new VariablesModel();
		$access_token = $vm->omit_user()->get( "fcb_access_token" );
		$tapp_id = $vm->omit_user()->get( "fcb_target_id" );
		if ($tapp_id != false)
			$tapp_id = substr($tapp_id, 1);
		
		$include_url = $vm->omit_user()->get( "fcb_inc_url" );
		$form = new Form( "administrace/nastaveni/ulozit-facebook" );
		$form->addCheckbox( "fcb_include_url", "ano", $include_url ==  "ano" ? TRUE : FALSE)
				  ->addText( "fcb_access_token",null,200,200 )
				  ->set_value( $access_token )
				  ->set_attribute( "class", "span12" )
				  ->addText( "fcb_target_app_id" )
				  ->set_value( $tapp_id )
				  ->set_attribute( "class", "span4" );


		return $form;
	}

	public function ulozit_facebook()
	{
		$vm = new VariablesModel();
		$access_token = $this->input->post("fcb_access_token");
		$tapp_id = $this->input->post("fcb_target_app_id");
		$include_url = $this->input->post("fcb_include_url");
		
		$vm->omit_user()->set("fcb_access_token", $access_token);
		$vm->omit_user()->set("fcb_target_id", "#".$tapp_id);
		$vm->omit_user()->set("fcb_inc_url", $include_url != false ? "ano" : "ne");
		$this->session->set_flashdata("admin","Nastavení facebook sdílení proběhlo v pořádku");
		
		
		redirect("administrace/nastaveni");
		
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * Comp...
 * @author Pavel Vais
 * @property CI_Loader $load
 * @property CI_Form_validation $form_validation
 * @property CI_Input $input
 * @property My_Output $output
 * @property CI_Email $email
 * @property CI_URI $uri
 * @property CI_DB_active_record $db
 * @property Header $header
 * @property Template $template
 * @property Tank_auth $tank_auth
 * @property Roles $roles
 */
class Comp extends My_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library( 'message' );
	}

	public function link_users()
	{
		$this->load->library( "template" );
		$data['form'] = $this->_create_form_link_users( $this->input->post( "id" ) );

		$this->output->json_append( 'response', $this->load->view( "templates/administrace/tmpl_link_users", $data, TRUE ) )
			   ->json_flush();
	}

	public function ban_user()
	{
		$this->load->library( "template" );
		$data['form'] = $this->_create_form_ban_user( $this->input->post( "id" ), $this->input->post( "ban" ) == "1" ? TRUE : FALSE  );

		$this->output->json_append( 'response', $this->load->view( "templates/administrace/tmpl_ban_user", $data, TRUE ) )
			   ->json_flush();
	}

	private function _create_form_link_users($id)
	{
		$form = new Form( "administrace/uzivatele/propojeni" );
		$form->addText( "username", "username (login):", 20, 20 )
			   ->set_rule( FORM::RULE_FILLED, "Pro spojení účtů musíte napsat název druhého účtu." )
			   ->addHidden( "source_id", $id )
			   ->setSubmit( "submit", "spojit účty" )
			   ->set_attribute( "class", "button link" );

		return $form;
	}

	private function _create_form_ban_user($id, $ban = true)
	{
		$form = new Form();

		if ( $ban )
			$form->addText( "ban_reason", "Důvod:", 30, 30 );

		$form->addHidden( "ban", $ban ? 1 : 0  )
			   ->addHidden( "id", $id );

		$form->setDestination( "administrace/uzivatele/zabanovat" )
			   ->setSubmit( "submit", ($ban ? "zabanovat" : "odbanovat" ) )
			   ->set_attribute( "submit", "class", "button " . ($ban ? "ban" : "unban") );

		return $form;
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
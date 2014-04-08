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
 */
class Login extends MY_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->helper( array('form', 'url') );
		$this->load->library( 'session' );
		$this->load->library( 'message' );
		$this->load->language( 'authorization' );
	}

	/**
	 * Odlhaseni uzivatele z administrace / z kompletni stranky
	 */
	public function out()
	{
		if ( User::is_logged_in() )
			User::logout();
		redirect( 'administrace/login' );
	}

	/**
	 * Login user on the site
	 *
	 * @return void
	 */
	function index()
	{
		if ( User::is_logged_in() )
		{
			if ( !$this->roles->allowed( "administration", "access" ) )
			{
				// Todo: LOG!!!
				$data['errors'] = 'Nemáte oprávnění vstoupit do administrace. <br>(' . User::get_username() . ")";
				$data['form'] = $this->createFormLogin();
				$this->load->view( 'administrace/view_login', $data );
			}
			else
			{
				redirect( 'administrace' );
			}
		}

		$data['form'] = $this->createFormLogin();
		$this->load->view( 'administrace/view_login', $data );
	}

	public function logmein()
	{
		$login = $this->input->post( "login" );
		$password = $this->input->post( "password" );
		$remember = $this->input->post( "remember" );

		try
		{
			User::login( $login, $password, $remember == true ? true : false  );

			if ( !$this->roles->allowed( "administration", "access" ) )
			{
				//= Nema opravneni vstoupit do administrace!!!
				$this->session->set_flashdata( "admin", "Nemáte přístup do administrace" );
				redirect( 'administrace/login' );
			}


			$this->session->set_flashdata( "admin", $this->lang->line( "auth.logged_in" ) );
			redirect( 'administrace/' );
		}
		catch (Exception $exc)
		{
			$this->session->set_flashdata( "admin", $exc->getMessage() );
			redirect( 'administrace/login' );
		}
	}

	private function createFormLogin()
	{
		$form = new Form( 'administrace/login/logmein' );
		$form->addText( 'login', 'Login', 30, 50 )
				  ->set_rule( Form::RULE_FILLED, 'Login is required.' )
				  ->set_rule( Form::RULE_MIN_CHARS, 'Login must have at least %argument% chars.', 5 )
				  ->set_attribute( 'placeholder', 'uživatelské jméno' )
				  ->set_attribute( 'class', 'form-control' )
				  ->set_form_attribute('class', 'form-horizontal');

		$form->addPassword( 'password', 'Password', 30 )
				  ->set_rule( Form::RULE_FILLED, 'Password is required.' )
				  ->set_attribute( 'class', 'form-control' )
				  ->set_attribute( 'placeholder', 'heslo' );

		$form->addCheckbox( 'remember', 1, false, "zapamatovat si mě" );
		$form->set_form_attribute( "class", "form-horizontal" );

		$form->setSubmit( 'submit', 'Přihlásit se' )
				  ->set_attribute( 'class', 'btn btn-large btn-primary' );
		return $form;
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
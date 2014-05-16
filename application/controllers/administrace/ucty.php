<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * Administracni rozcestnik
 * @author Pavel Vais
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_Email $email
 * @property CI_URI $uri
 * @property CI_DB_active_record $db
 * @property Header $header
 * @property Message $message
 * @property Menu $menu
 * @property Tank_auth $tank_auth
 * @property Roles $roles
 * @property Template $temaplate
 */
class Ucty extends My_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->library( array('message', 'template', 'roles') );
	}

	public function index()
	{
		$this->zmena_hesla();
	}

	public function zmena_hesla()
	{
		$cm = new ConfessionModel();
		$hm = new HashtagsModel();
		$data['count_confessions'] = $cm->count();
		$data['count_hashtags'] = $hm->count();
		$form = new Form( "administrace/ucty/ulozit-ucet" );
		$form->addText( "acc_username" )
				  ->setValue( User::get_username() )
				  ->setAttribute( "class", "span4" )
				  ->setRule( "nospace", "Uživatelské jméno nesmí obsahovat mezeru." )
				  ->setRule( "onlychars", "Uživatelské jméno musí obsahovat jenom písmena a číslice" )
				  ->setRule( Form::RULE_MIN_CHARS, "Uživatelské jméno musí mít alespoň %argument% písmen",5 )
				  ->setRule( Form::RULE_FILLED, "Uživatelské jméno musí být vyplněné" )
				  ->set_form_attribute( "id", "form-edit" )
				  ->addText( "acc_pass1" )
				  ->setAttribute( "class", "span4" )
				  ->setAttribute( "placeholder", "nové heslo" )
				  ->addText( "acc_pass2" )
				  ->setAttribute( "placeholder", "zopakovat nové heslo" )
				  ->setRule( Form::RULE_SAME_AS, "Hesla se neshodují", 'acc_pass1' )
				  ->setAttribute( "class", "span4" )
				  ->addHidden( "user_id", User::get_id() )
				  ->setSubmit( "save", "Uložit přiznání" );
		$data['form'] = $form;

		$this->load->view( 'administrace/ucty/view_zmena_hesla', $data );
	}

	public function ulozit_ucet()
	{
		$new_username = $this->input->post( "acc_username" );
		$new_pass = $this->input->post( "acc_pass1" );
		$new_pass2 = $this->input->post( "acc_pass2" );
		$user_id = $this->input->post( "user_id" );
		if ( $new_pass != $new_pass2 )
		{
			$this->session->set_flashdata( "error", "Nově zadaná hesla se neshodují!" );
			redirect( "administrace/ucty/zmena_hesla" );
		}

		if ( User::get_id() != $user_id )
		{
			$this->session->set_flashdata( "error", "Nastal problém se změnou účtu. Nic se nezměnilo." );
			redirect( "administrace/ucty/zmena_hesla" );
		}

		if ( isset( $new_pass[1] ) )
		{
			User::change_password( $new_pass );
		}


		if ( $new_username != User::get_username() )
		{
			$um = new \UsersModel;
			if ( $um->get_user_by_username( $new_username ) != FALSE )
			{
				//= sername je jiz zabrany
				$this->session->set_flashdata( "error", "Daný username je již zabraný, vyberte prosím jiný." );
				redirect( "administrace/ucty/zmena_hesla" );
			}

			$um->change_value( User::get_id(), "username", $new_username );
			User::reload_session();

			$msg = isset( $new_pass[1] ) ? "Úspěšně jste změnili login i heslo." : "Úspěšně jste změnili název účtu.";

			$this->session->set_flashdata( "admin", $msg );
			redirect( "administrace/ucty/zmena_hesla" );
		}

		if ( isset( $new_pass[1] ) )
		{
			$this->session->set_flashdata( "admin", "Úspěšně jste změnili heslo ke svému účtu." );
			redirect( "administrace/ucty/zmena_hesla" );
		}

		$this->session->set_flashdata( "admin", "Nic nebylo změněno." );
		redirect( "administrace/ucty/zmena_hesla" );
	}

	private function seznam()
	{
		$um = new UsersModel;

		$data['users'] = $um->get_all_users();
		$data['roles'] = json_encode( $this->roles->get_roles() );
		$data['filter_form'] = $this->_create_form_filter( null );

		$this->load->library( 'pagination' );

		$config['base_url'] = base_url( "administrace/uzivatele/filter/" );
		$config['total_rows'] = $um->count_users();
		$config['per_page'] = UsersModel::ROWS_PER_PAGE;
		$config['first_link'] = " &#171;";
		$config['last_link'] = "&#187;";
		$config['next_link'] = FALSE;
		$config['prev_link'] = FALSE;
		$this->pagination->initialize( $config );

		$this->load->view( 'administrace/uzivatele/view_seznam', $data );
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
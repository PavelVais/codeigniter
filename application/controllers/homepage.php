<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_URI $uri
 * @property My_Output $output
 * @property CI_DB_active_record $db
 * @property MY_Lang $lang
 */
class Homepage extends My_Controller {

	private $contactEmail = 'daw.hk@seznam.cz';
	
	function __construct() {
		parent::__construct();
		$this->load->helper( 'text' );
	}

	/**
	 *
	 */
	public function index() {
		$data = array();
		$this->load->view( 'homepage/view_index', $data );
	}

	public function we_are_working_on_it() {
		$this->lang->view( 'homepage/view_maintenance' );
	}

	public function contact_us() {
		
		$emailSender = new EmailForm();

		if ( !$emailSender->send( $this->contactEmail ) ) {
			log_message( 'error', 'Kontaktní formulář neumožnuje posílat email na danou adresu!' );
			$this->output->json_append( 'respond', 'Zpráva nebyla úspěšně odeslána.', 500 );
		}
		else {

			if ( $this->input->is_ajax_request() ) {
				$this->output->json_append( 'respond', 'Zpráva byla úspěšně odeslána.' );
			}
			else {
				FlashMessage::set( 'Zpráva byla úspěšně odeslána.' );
				redirect( '' );
			}
		}
		$this->output->json_flush();
	}

	public function database() {
		$TM = new TestModel;

		Dump( $TM->get_all() );
		$this->lang->view( 'homepage/view_maintenance' );
		//FB::info($TM->get_all(),'return');
	}

}

/* End of file homepage.php */
/* Location: ./application/controllers/homepage.php */
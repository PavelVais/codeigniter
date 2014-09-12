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
class Errors extends My_Controller {

	public function __construct() {
		parent::__construct();
	}

	public function index() {
		$this->load->helper( 'text' );
		\Head\Head2::addCSS( 'mystyle.css' );
		$EM = new ErrorModel();
		$data['errors'] = $EM->getAll();

		$this->load->view( "administrace/errors/view_index", $data );
	}

	/**
	 * Vymaze chybovou hlasku
	 * @param int $id
	 */
	public function deleteErrorMessage($id) {
		$EM = new ErrorModel();
		if ( $EM->deleteByID( $id ) ) {
			$this->output->json_append( 'response', 'zpráva byla smazána.' );
		}
		else {
			$this->output->json_append( 'response', 'zpráva nebyla smazána', 500 );
		}
		$this->output->json_flush();
	}

	/**
	 * Vymaze vsechny chyby
	 */
	public function deleteErrorMessages() {
		$EM = new ErrorModel();
		if ( $EM->flush() ) {
			$this->output->json_append( 'response', 'Chybné hlášky byly smazány.' );
		}
		else {
			$this->output->json_append( 'response', 'Chybné hlášky nebyly smazány.', 500 );
		}
		$this->output->json_flush();
	}

	/**
	 * Vsechny chyby prevede jako prectene
	 */
	public function makeErrorsViewed() {
		$EM = new ErrorModel();
		$EM->makeViewed();
		$this->output->json_append( 'response', 'chybné hlášky byly označeny jako přečtené.' );
		$this->output->json_flush();
	}

	public function sendReport() {
		$this->load->helper( 'text' );
		$EM = new ErrorModel();
		$data['errors'] = $EM->getAll();
		$this->load->view( "administrace/errors/view_report", $data );
	}

}

/* End of file homepage.php */
/* Location: ./application/controllers/homepage.php */
<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 */
class EmailSend extends My_Controller {

	public function __construct() {
		parent::__construct();
	}

	public function index() {

		$e = new EmailForm();
		
		echo $e->printContactForm()->generate();
	}

}

/* End of file homepage.php */
/* Location: ./application/controllers/homepage.php */
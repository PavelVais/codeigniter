<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_URI $uri
 * @property CI_DB_active_record $db
 * @property Header $header
 * @property Menu $menu
 * @property Tank_auth $tank_auth //sprava prihlasenych
 * @property Template $template
 * @property Message $message
 * @property MY_Calendar $calendar
 * @property CI_Session $session
 * @property GoogleAnalytics $googleanalytics
 */
class Homepage extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->helper( 'text' );
	}

	public function index()
	{

		$form = new Form( "confession/add" );
		$form->addTextArea( "txt_confession" )
				  ->set_attribute( "class", "" )
				  ->set_attribute( "id", "input-confession" )
				  ->set_rule(Form::RULE_FILLED, "Confession can't be empty.")
				  ->set_rule(Form::RULE_MIN_CHARS, "Confession must have at least %argument% characters.",10)
				  ->addText( "inp_hashtag" )
				  ->set_attribute( "class", "" )
				  ->set_attribute( "id", "input-hashtag" )
				  ->set_rule( "nospace", "Hashtag can't contain spaces." )
				  ->set_rule( "onlychars", "Hashtag must contain only characters." )
				  ->set_attribute( "placeholder", "paste your hashtag" )
				  ->set_form_attribute( "id", "form-makeconfession" );


		$data['form'] = $form;

		$this->load->view( 'homepage/view_index', $data );
	}

}

/* End of file homepage.php */
/* Location: ./application/controllers/homepage.php */
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
 * @property MY_Lang $lang
 */
class Form extends My_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->helper( 'text' );
	}

	/**
	 *
	 */
	public function index()
	{
		Benchmark\Timer::start( 'forms 2.0!!' );

		$form = new Form\Form( '#' );

		$form->addText( 'testuji', 'TEST1:' );
		$form->addText( 'etestuji', 'LABEL2:' );
		$form->addHidden( 'teefgestuji', 'MRRAT:' );
		$form->addText( 'testueffeji', 'MAAT:' );
		$form->addTextArea( 'fdowe', 'Labeeel!' );
		$form->setSubmit( 'save', 'Uložit' );
		\Benchmark\Timer::mark( 'form build finish' );
		$generator = new \Form\Generator( $form );

		$generator->setTemplate( new \Form\BootstrapTemplate() );
		$generator->template->setOnce( \Form\BootstrapTemplate::OPTION_INPUT_CLASS, 'col-sm-7', 'etestuji' )
				  ->setOnce( \Form\BootstrapTemplate::OPTION_ROW_TYPE, \Form\BootstrapTemplate::ERROR, 'testueffeji' );

		/* $generator->exclude($elementName);
		  $generator->dontClose();
		  $generator->generate();


		  $generator->open();
		  $generator->generate($elementName);
		  $generator->generateLabel($elementName);
		  $generator->generateElement($elementName);
		  $generator->template->setOnce($name,$value);
		  $generator->generateElement($elementName);
		  $generator->close(); */

		$data['generator'] = $generator;
		//$generator->generateRange($elementFrom,$elementTo);
		$this->load->view( 'dev/view_form', $data );

		Benchmark\Timer::stop();
	}

	public function index2()
	{
		$form = new Form\Form( '#' );
		$form->addText( 'testuji', 'TEST1:' );
		$form->addText( 'etestuji', 'TEST2 kratší:' );
		$form->addHook( 'vnořený hook!' );
		$form->addHook( 'další <i class="fa fa-times"></i> hook s <strong>html prvky</html>!', 'hook label' );
		$form->addHidden( 'teefgestuji', 'MRRAT:' );
		$form->addText( 'testueffeji', 'MAAT:' );
		$form->addTextArea( 'fdowe', 'Labeeel!' );
		$form->setSubmit( 'save', 'Uložit' )
				  ->setAttribute( 'class', 'btn btn-primary btn-block' );
		$generator = new \Form\Generator( $form );

		$generator->setTemplate( new \Form\BootstrapTemplate() );
		$generator->template->setOnce( \Form\BootstrapTemplate::OPTION_INPUT_CLASS, 'col-sm-7', 'etestuji' )
				  ->setOnce( \Form\BootstrapTemplate::OPTION_ROW_TYPE, \Form\BootstrapTemplate::ERROR, 'testueffeji' );

		$data['generator'] = $generator;
		//$generator->generateRange($elementFrom,$elementTo);
		$this->load->view( 'dev/view_form2', $data );
	}

	public function validate()
	{

		$form = new Form\Form( 'dev/form/ajaxresponse' );
		$form->addText( 'testuji', 'TEST1:' )
				  ->setRule( \Form\Form::RULE_MIN_CHARS, 'min. 5 charů!!!', 5 )
				  ->setRule( \Form\Form::RULE_NUMBER, 'čísla to musí byt!!!' );
		$form->addText( 'pass', 'email:' )
				  ->setRule( \Form\Form::RULE_EMAIL, 'email to musí byt' );
		$form->addText( 'ccc', 'CUSTOM' )
				  ->setRule( 'custom', 'Musí se rovnat "EEE', 'EEE' );

		$form->set_form_attribute( 'id', 'validatorform' );
		$form->setSubmit( 'save', 'Uložit' )
				  ->setAttribute('class', 'btn btn-primary');
		$generator = new \Form\Generator( $form );

		$generator->setTemplate( new \Form\BootstrapTemplate() );
		$generator->template->setOnce( \Form\BootstrapTemplate::OPTION_INPUT_CLASS, 'col-sm-7', 'etestuji' )
				  ->setOnce( \Form\BootstrapTemplate::OPTION_ROW_TYPE, \Form\BootstrapTemplate::ERROR, 'testueffeji' );

		$data['generator'] = $generator;
		
		
		
		$form = new Form\Form( 'dev/form/ajaxresponse' );
		$form->addText( 'form2el1', 'TEST1:' )
				  ->setRule( \Form\Form::RULE_MIN_CHARS, 'min. 5 charů!!!', 5 )
				  ->setRule( \Form\Form::RULE_NUMBER, 'čísla to musí byt!!!' );
		$form->addText( 'form2el2', 'email:' )
				  ->setRule( \Form\Form::RULE_EMAIL, 'email to musí byt' );
		$form->addText( 'form2el3', 'CUSTOM' )
				  ->setRule( 'custom', 'Tato položka musí být %argument%', 'EEE' );
		$form->addText( 'form2el4', 'CUSTOM AJAX' )
				  ->setRule( 'ajax', 'Aajx v piči', site_url('dev/form/ajaxresponse') );

		$form->set_form_attribute( 'id', 'form2test' );
				  
		$form->setSubmit( 'save', 'Uložit' )
				  ->setAttribute('class', 'btn btn-primary');
		$generator = new \Form\Generator( $form );

		$generator->setTemplate( new \Form\BootstrapTemplate() );
		$generator->template->setOnce( \Form\BootstrapTemplate::OPTION_INPUT_CLASS, 'col-sm-7', 'etestuji' )
				  ->setOnce( \Form\BootstrapTemplate::OPTION_ROW_TYPE, \Form\BootstrapTemplate::ERROR, 'testueffeji' );

		$data['generator2'] = $generator;
		
		Head::remove_from_container( 'js' );
		Head::add()->js( 'http://code.jquery.com/jquery-1.11.0.min.js' )
				  ->js( 'bootstrap.min.js' )
				  ->js( 'formValidation.js' );
		$this->load->view( 'dev/view_form3', $data );
	}
	
	function ajaxresponse()
	{
		sleep(2);
		return true;
	}

}

/* End of file homepage.php */
/* Location: ./application/controllers/homepage.php */
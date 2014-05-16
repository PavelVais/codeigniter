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
class Form extends My_Controller {

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
		Benchmark\Timer::start('forms 2.0!!');
		
		$form = new Form\Form('#');
		$form->addText('testuji','TEST1:');
		$form->addText('etestuji','LABEL2:');
		$form->addHidden('teefgestuji','MRRAT:');
		$form->addText('testueffeji','MAAT:');
		$form->addTextArea('fdowe', 'Labeeel!');
		$form->setSubmit('save', 'Uložit');
		\Benchmark\Timer::mark('form build finish');
		$generator = new \Form\Generator($form);
		
		$generator->setTemplate(new \Form\BootstrapTemplate());
		$generator->template->setOnce(\Form\BootstrapTemplate::OPTION_INPUT_CLASS, 'col-sm-7', 'etestuji')
				  ->setOnce(\Form\BootstrapTemplate::OPTION_ROW_TYPE,\Form\BootstrapTemplate::ERROR,'testueffeji');
		
		/*$generator->exclude($elementName);
		$generator->dontClose();
		$generator->generate();
		
		
		$generator->open();
		$generator->generate($elementName);
		$generator->generateLabel($elementName);
		$generator->generateElement($elementName);
		$generator->template->setOnce($name,$value);
		$generator->generateElement($elementName);
		$generator->close();*/
		
		$data['generator'] = $generator;
		//$generator->generateRange($elementFrom,$elementTo);
		$this->load->view('dev/view_form',$data);
		
		Benchmark\Timer::stop();
	}
	
	public function index2()
	{
		$form = new Form\Form('#');
		$form->addText('testuji','TEST1:');
		$form->addText('etestuji','TEST2 kratší:');
		$form->addHook('vnořený hook!');
		$form->addHook('další <i class="fa fa-times"></i> hook s <strong>html prvky</html>!','hook label');
		$form->addHidden('teefgestuji','MRRAT:');
		$form->addText('testueffeji','MAAT:');
		$form->addTextArea('fdowe', 'Labeeel!');
		$form->setSubmit('save', 'Uložit')
				  ->setAttribute('class', 'btn btn-primary btn-block');
		$generator = new \Form\Generator($form);
		
		$generator->setTemplate(new \Form\BootstrapTemplate());
		$generator->template->setOnce(\Form\BootstrapTemplate::OPTION_INPUT_CLASS, 'col-sm-7', 'etestuji')
				  ->setOnce(\Form\BootstrapTemplate::OPTION_ROW_TYPE,\Form\BootstrapTemplate::ERROR,'testueffeji');
		
		$data['generator'] = $generator;
		//$generator->generateRange($elementFrom,$elementTo);
		$this->load->view('dev/view_form2',$data);
	}

}

/* End of file homepage.php */
/* Location: ./application/controllers/homepage.php */
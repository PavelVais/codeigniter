<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
// ------------------------------------------------------------------------

/**
 * CodeIgniter Application Controller Class
 *
 * This class object is the super class that every library in
 * CodeIgniter will be assigned to.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/general/controllers.html
 * @property MY_Lang $lang
 */
class My_Controller extends CI_Controller
{

	/**
	 * Rozlisuje, jestli se nachazime v administraci, nebo ne
	 * @var Boolean
	 */
	private $is_administration = FALSE;
	
	/**
	 * @var Navigation\Navigator 
	 */
	public $navigator;

	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
		//include APPPATH.'libraries/components/HTML/Element';
		\Head\Head2::init();
		Logs::init();
		User::init();
		Retina::init();

		if ( $this->uri->segment( 1 ) === "administrace" )
		{
			$this->is_administration = TRUE;
			$this->load->database();
			//$this->administration_bootstrap();
		}
		
		$this->navigator = new Navigation\Navigator();

		
	}
	
	/**
	 * Vlozi do view souboru promennou
	 * @param string $name - nazev promenne
	 * @param mixed $value - hodnota
	 * @return \My_Controller
	 */
	function layout($name, $value)
	{
		$this->load->vars( array($name => $value) );
		return $this;
	}

	function administration_bootstrap()
	{
		if ( User::is_logged_in() == FALSE && $this->uri->segment( 2 ) !== 'login' )
			redirect( "administrace/login" );

		if ( $this->is_administration && $this->uri->segment( 2 ) !== 'login' )
		{
			$this->load->library( "template" );
			$this->menu->setGroup( 'administrace-hlavni' );

			//= Pokud nejde o ajaxovy dotaz, pripravi se menu (nacte z cache)
			if ( !$this->input->is_ajax_request() )
				$this->template->add_tag( "url", $this->uri->segment( 2 ) )
						  ->load( "administrace/tmpl_administrace_menu" );
		}
	}

}

// END Controller class

/* End of file Controller.php */
/* Location: ./system/core/Controller.php */
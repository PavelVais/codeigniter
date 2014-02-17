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
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
		Autoloader::loadStatic( 'helpers/head_helper', "head::init" );
		Autoloader::loadStatic( 'libraries/User', 'User::init' );
		Autoloader::loadStatic( 'models/logs', 'Logs::init' );

		if ( $this->uri->segment( 1 ) === "administrace" )
		{
			$this->is_administration = TRUE;
			$this->administration_bootstrap();
		}

		if ( $this->lang->lang() == "en" )
		{
			Head::remove()->js( 'http://www.myslimnatebe.cz/javascript/file/mnt_javascript.php' );
			Head::add()->js( array(
				 'url' => 'http://www.wisheer.com/javascript/file/mnt_javascript.php',
				 'except' => "administrace"
			) );
		}
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
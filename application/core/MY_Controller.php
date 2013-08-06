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
 */
class My_Controller extends CI_Controller
{

	private static $instance;

	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();

		if ( !User::is_logged_in() )
		{
			redirect( 'administrace/login' );
		}
		$this->load->library( "template" );
		$this->menu->setGroup( 'administrace-hlavni' );

		//= Pokud nejde o ajaxovy dotaz, pripravi se menu (nacte z cache)
		/*if ( !$this->input->is_ajax_request() )
			$this->template->add_tag( "url", $this->uri->segment( 2 ) )
					  ->load( "administrace/tmpl_administrace_menu" );*/
	}

}

// END Controller class

/* End of file Controller.php */
/* Location: ./system/core/Controller.php */
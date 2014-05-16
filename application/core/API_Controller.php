<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

class API_Controller extends CI_Controller
{
	/**
	 * @var CI_Controller
	 */
	protected $ci;
	
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->ci = & get_instance();
		
	}
	
	function parseMethod()
	{
		
	}
	
	

}

// END API_Controller class

/* End of file API_Controller.php */
/* Location: ./core/Controller.php */
<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * CodeIgniter Config Class
 *
 * Tento config je upraveny pro potreby poustet stranku ze subdomeny
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/config.html
 */
class MY_Config extends CI_Config
{

	/**
	 * Constructor
	 *
	 * Sets the $config data from the primary config.php file as a class variable
	 *
	 * @access   public
	 * @param   string	the config file name
	 * @param   boolean  if configuration values should be loaded into their own section
	 * @param   boolean  true if errors should just return false, false if an error message should be displayed
	 * @return  boolean  if the file was successfully loaded or not
	 */
	function __construct()
	{
		parent::__construct();
		$this->config = & get_config();
		log_message( 'debug', "Custom config Class Initialized" );

		if ( isset( $_SERVER['HTTP_HOST'] ) )
		{

			$base_url = isset( $_SERVER['HTTPS'] ) && strtolower( $_SERVER['HTTPS'] ) !== 'off' ? 'https' : 'http';
			$base_url .= '://' . $_SERVER['HTTP_HOST'];

			/* $array = explode( '/', $_SERVER['SCRIPT_NAME'] );

			  $i = 0;
			  for ( $index = count( $array ) - 1; $index > 0; $index-- )
			  {
			  $i++;
			  if ($i == 2)
			  {
			  $base_url .= '/'.$array[$index];
			  echo ($array[$index]);
			  break;
			  }
			  } */
			if ( strpos( $_SERVER['SCRIPT_NAME'] , 'subdom' ) === false )
			{
				$base_url .= str_replace( basename( $_SERVER['SCRIPT_NAME'] ), '', $_SERVER['SCRIPT_NAME'] );
			}
				
		}
		else
		{
			$base_url = 'http://localhost/';
		}
		$this->set_item( 'base_url', $base_url );
	}

}

// END CI_Config class

/* End of file Config.php */
/* Location: ./system/core/Config.php */
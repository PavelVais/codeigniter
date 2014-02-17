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
 * CodeIgniter Application Output Class
 *
 * Tato trida rozsiruje puvodni o moznost lepe nakladat s json odpovedma 
 * na ajaxovy dotazy. Diky tomu je mozne pomoci hooku zasahovat
 * do vysledneho zobrazeni ajaxu
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/general/controllers.html
 */
class My_Output extends CI_Output
{

	private $json_data;
	private $header_code = 200;

	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
	}

	function json_append($name, $value, $code = 200)
	{
		//if (is_array($value))
		//$value = array_map('utf8_encode', $value);
		//else $value = utf8_encode($value);

		$this->json_data[$name] = $value;
		if ( $code > 200 )
			$this->header_code = $code;

		return $this;
	}

	function json_flush()
	{
		set_status_header( $this->header_code );
		$this->set_output( json_encode(  $this->json_data  ) );

		return $this;
	}

	private function utf8ToJSON($inArray)
	{
		static $depth = 0;

		/* our return object */
		$newArray = array();

		/* safety recursion limit */
		$depth ++;
		if ( $depth >= '30' )
		{
			return false;
		}

		/* step through inArray */
		foreach ( $inArray as $key => $val )
		{
			if ( is_array( $val ) )
			{
				/* recurse on array elements */
				$newArray[$key] = $this->utf8ToJSON( $inArray );
			}
			else
			{
				/* encode string values */
				$newArray[$key] = utf8_encode( $val );
			}
		}

		/* return utf8 encoded array */
		return $newArray;
	}

}

// END Controller class

/* End of file Controller.php */
/* Location: ./system/core/Controller.php */
<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * API_Controller
 */
class API_Controller extends My_Controller
{

	private $secured;
	private $posts;

	/**
	 * Constructor
	 */
	function __construct($secured = false)
	{
		parent::__construct();
		$this->secured = $secured;
		Head::init();
		Logs::init();
		$this->init();
	}

	private function init()
	{
		if ( $this->secured && !User::is_logged_in() )
		{
			$this->sendResponse( 'Uživatel nemá povolení provést danou akci.', 401 );
		}
		$this->posts = $this->input->post();
	}

	public function raiseException($rule, $method)
	{
		$roles = new Roles;
		if ( !$roles->allowed( $rule, $method ) )
		{
			$this->sendResponse( 'Uživatel nemá povolení provést danou akci.', 401 );
		}
	}

	public function appendResponse($name, $value, $code = 200)
	{
		$this->output->json_append( $name, $value, $code );
		return $this;
	}

	public function sendResponse($message = null, $code = 200)
	{
		if ( $message != null )
		{
			$this->output->json_append( 'response', $message, $code );
		}
		$this->output->json_flush();
		die( $this->output->get_output() );
	}

	public function getArgument($name, $defaultValue = null, $allowedOptios = array())
	{
		if ( !isset( $this->posts[$name] ) )
		{
			if ( $defaultValue === null )
				$this->sendResponse( 'Argument ' . $name . ' je vyžadován.', 500 );
			else
				return $defaultValue;
		}

		if ( !$this->validateArgument( $this->posts[$name], $allowedOptios ) )
		{
			$this->sendResponse( 'Argument ' . $name . ' nabývá nepřijatelné hodnoty.', 500 );
		}
		
		//= Pretypujeme ze stringu na boolean
		if (strtolower($this->posts[$name]) === 'true')
			$this->posts[$name] = true;
		elseif (strtolower($this->posts[$name]) === 'false')
			$this->posts[$name] = false;
		
		return $this->posts[$name];
	}

	private function validateArgument($argument, $options)
	{
		if ( empty( $options ) )
			return true;

		if ( !is_array( $options ) )
		{
			$options = explode( ',', $options );
			foreach ( $options as &$opt )
			{
				$opt = trim( $opt );
				unset( $opt );
			}
		}

		foreach ( $options as $opt )
		{
			if ( $opt == ':number:' )
			{
				return is_numeric( (int)$argument );
			}
			if ( strtolower( $opt ) == strtolower( $argument ) )
			{
				return true;
			}
		}
		return false;
	}

}

// END Controller class

/* End of file Controller.php */
/* Location: ./system/core/Controller.php */
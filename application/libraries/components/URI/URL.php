<?php

namespace URI;

/**
 * Description of URL
 * @author Pavel Vais
 */
class URL
{

	const PREFIX_THIS_MODUL = '!';
	const PREFIX_ADD_LINK = '$';
	const PREFIX_EXTERNAL_LINK = ':';
	const PREFIX_SECURE_LINK = '?';

	private $modul = '';
	private $arguments = '';
	private $url = '';
	private $is_external = false;

	/**
	 * Pokud se string (neboli budouci url) zada s "!" na zacatku, 
	 * bude se povazovat prvni segment URI jako modul a automaticky se k url pripise
	 * @param type $string
	 * @param type $arguments
	 */
	public function __construct($string, $arguments = null)
	{
		if ( $arguments != null )
			$this->setArguments( $arguments );

		$this->analyzeURI( $string );

		$this->url = $string;
	}

	public function setArguments($data)
	{
		if ( !is_array( $data ) )
		{
			$data = array($data);
		}
		foreach ( $data as $name => $value )
		{
			if ( !is_numeric( $name ) )
			{
				$this->arguments .= '/' . $name . '/' . $value;
			}
			else
			{
				$this->arguments .= '/' . $value;
			}
		}
		$this->arguments = str_replace( '//', '/', $this->arguments );
		return $this;
	}

	public function get()
	{
		if ( $this->is_external )
			return $this->url . $this->arguments ;
		else
			return site_url( $this->modul . $this->url . $this->arguments );
	}

	private function assignModul()
	{
		$ci = & get_instance();
		$s = $ci->uri->segment( 1 );

		$this->modul = $s . '/';
	}

	function create()
	{
		
	}

	public function analyzeURI(&$string)
	{
		$identifier = $string[0];
		if ( $identifier == self::PREFIX_THIS_MODUL )
		{
			$string = substr( $string, 1 );
			$this->assignModul();
			return $this->analyzeURI( $string );
		}
		elseif ( $identifier == self::PREFIX_EXTERNAL_LINK )
		{
			$string = self::external_url( substr( $string, 1 ) );
			$this->is_external = true;
			return $this->analyzeURI( $string );
		}
		elseif ( $identifier == self::PREFIX_SECURE_LINK )
		{
			$string = substr( $string, 1 );
			$this->setArguments( \Secure::csrf_get() );
			return $this->analyzeURI( $string );
		}
		elseif ( $identifier == self::PREFIX_ADD_LINK )
		{
			$string = substr( $string, 1 );
			$string = current_url().'/'.$string; 
			$this->is_external = true;
			return $this->analyzeURI( $string );
		}
	}

	static function external_url($string)
	{

		if ( $string == 'http://' OR $string == '' )
		{
			return '';
		}

		$url = parse_url( $string );

		if ( !$url OR !isset( $url['scheme'] ) )
		{
			$string = 'http://' . $string;
		}
		return $string;
	}

	/* static function registerModuls($namesArray)
	  {
	  if ( !is_array( $namesArray ) )
	  {
	  $namesArray = array($namesArray);
	  }

	  self::registeredModuls = $namesArray;
	  } */
}

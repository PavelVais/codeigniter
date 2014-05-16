<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 */
class FlashMessage
{

	const INFO = "info";
	const WARNING = "warning";
	const ERROR = "danger";
	const SUCCESS = "success";
	const DEFAULT_NAME = "admin";

	static function setWithFontAwesomeIcon($message, $ico_name, $type = self::SUCCESS, $name = 'common')
	{
		self::set('<i class="fa '.$ico_name.'"></i> '.$message, $type, $name);
	}
	
	static function set($message, $type = self::SUCCESS, $name = 'common')
	{
		$ci = & get_instance();

		$div = \HTML\Element::open( 'div' )->addAttribute( 'class', 'alert' )
				  ->appendToAttribute( 'class', 'alert-' . $type );
		
		if ($message instanceof HTMLconstructor)
		{
			$div->append( $message );
		} else {
			$div->appendString($message);
		}

		$ci->session->set_flashdata( $name, $div->generate() );
	}

	static function get($name = 'common')
	{
		$ci = & get_instance();

		$output = $ci->session->flashdata( $name );
		return $output;
	}

}

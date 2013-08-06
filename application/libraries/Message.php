<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_URI $uri
 * @property Cache $cache
 */
class Message
{

	private $ci;

	const INFO = "info";
	const WARNING = "warning";
	const ERROR = "error";
	const SUCCESS = "success";
	const DEFAULT_NAME = "admin";

	private $class_prefix;
	private $wrappers;

	function __construct()
	{

		$this->ci = & get_instance();
		$this->ci->load->library( 'session' );
		$this->wrappers = array('ul', 'li');
		$this->class_prefix = "flash";
	}

	/**
	 * Nastavi obalovaci html prvky
	 * @param String $outher - default UL
	 * @param String $inner - default LI
	 * @return \Message 
	 */
	public function set_wrappers($outher, $inner)
	{
		$this->wrappers = array($outher, $inner);
		return $this;
	}

	/**
	 *
	 * @param String / Array $data - zprava / zpravy
	 * @param String $type - typ zpravy (INFO,WARNING,SUCCESS,ERROR)
	 * @param String $name - id zpravy
	 * @param boolean $group - ma se vlozit zpravy do ul -> li, nebo kazda zprava bude sama 
	 */
	public function append($data, $type = self::INFO, $name = self::DEFAULT_NAME, $group = TRUE)
	{
		if ( is_array( $data ) )
		{
			foreach ( $data as $key => $d )
			{
				if ( !$group )
				{
					$data[$key] = "<div class='" . $this->class_prefix . " " . $type . "'>" . $data[$key] . "</div>";
				}
				else
				{
					$data[$key] = "<" . $this->wrappers[1] . ">" . $d . "</" . $this->wrappers[1] . ">";
				}
			}


			if ( $group )
			{

				$data = implode( "\n", $data );
				$data = "<" . $this->wrappers[0] . ">" . $data . "</" . $this->wrappers[0] . ">";
				$data = "<div class='" . $this->class_prefix . " " . $type . "'>" . $data . "</div>";
				$this->ci->session->set_flashdata( $name, $data );
			}
			else
			{
				$this->ci->session->set_flashdata( $name, $data );
			}
		}
		else
		{
			$data = "<div class='" . $this->class_prefix . " " . $type . "'>" . $data . "</div>";
			$this->ci->session->set_flashdata( $name, $data );
		}
	}

	/**
	 * Vypise dany zpravy
	 * @param String $name - nazev flashdata 
	 */
	public function get($name = self::DEFAULT_NAME)
	{
		$output = $this->ci->session->flashdata( $name );

		if ( !is_array( $output ) )
		{
			$output = array($output);
		}

		foreach ( $output as $out )
		{
			echo $out . "\n";
		}
	}

}
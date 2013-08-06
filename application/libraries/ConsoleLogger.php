<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_URI $uri
 * @property Cache $cache
 */
class ConsoleLogger
{
	private $active_namespace = null;
	private $data_to_save;
	private $data = array();
	
	public function set_namespace($name)
	{
		$this->active_namespace = & $this->data[$name];
		
		return $this;
	}
	
	public function set_data($name,$value)
	{
				
		$this->data_to_save[$name] = $value;
		
		return $this;
	}
	
	public function new_row()
	{
		
		$this->active_namespace[] = $this->data_to_save;
		$this->data_to_save = null;
		
		return $this;
		
	}
	
	
	
	public function get_data_from_namespace($namespace_name)
	{
		return isset($this->data[$namespace_name]) ? $this->data[$namespace_name] : null;
	}

}
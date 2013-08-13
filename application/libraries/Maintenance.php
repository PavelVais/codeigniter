<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * Maintenance trida
 * Zarucuje, ze jakmile je v configu povolen maintenance mod,
 * stranky se pro navstevniky uzavrou
 * 
 */
class Maintenance
{

	/**
	 * Konstruktor
	 * @param type $currentPage
	 */
	function __construct()
	{
		$this->ci = & get_instance();

		$ip = $this->ci->input->ip_address();
		if ( $this->ci->config->item( 'maintenance_mode' ) && !in_array( $ip, $this->ci->config->item( 'maintenance_exclude_ip' ) ) && strpos($this->ci->uri->uri_string(), $this->ci->config->item( 'maintenance_url' )) === FALSE )
		{
			redirect($this->ci->config->item( 'maintenance_url' ) );
		}
	}

}

<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Cache Class
 *
 * Partial Caching library for CodeIgniter
 *
 * @category	Libraries
 * @author		Phil Sturgeon
 * @link		http://philsturgeon.co.uk/code/codeigniter-cache
 * @license		MIT
 * @version		2.1
 */

class EmailSender
{


	/**
	 * Constructor - Initializes and references CI
	 */
	function __construct()
	{
		$this->ci = & get_instance();
		
		
	}
	
	function send($target_email,$type,$title,$data)
	{
		$this->ci->load->config("tank_auth");
		$this->ci->load->library("email");
		
		$this->ci->email->from( "noreply@myslimnatebe.cz", "MyslimNaTebe.cz - noreply");
		$this->ci->email->to( $target_email );

		$this->ci->email->subject( $title );
		$this->ci->email->message( $this->ci->load->view( "comp/emails/$type", $data, TRUE ) );
		
		return $this->ci->email->send();
	}

	
}

/* End of file Cache.php */
/* Location: ./application/libraries/Cache.php */
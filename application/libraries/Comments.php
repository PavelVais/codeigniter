<?php

/**
 * Description of Comments
 *
 * @author Pavel Vais
 */
class Comments
{
	/**
	 *
	 * @var DiskuzeModel
	 */
	private $diskuze;
	
	function __construct()
	{
		$this->ci = & get_instance();
		$this->ci->load->library( 'pagination' );
		$this->ci->load->helper( 'url' );
		
		
		
	}
	
	function load()
	{
		$this->diskuze = new DiskuzeModel();
		$strana = $this->uri->segment( 3 ) > 0 ? $this->uri->segment( 3 ) : 1;
	}
	
	function generate()
	{
		
	}
	
	private function build_pagination()
	{
		$config['base_url'] = site_url( 'administrace/diskuze/strana/' );
		$config['total_rows'] = $this->diskuze->getDiskuzeSize()->pocet;
		$config['per_page'] = DiskuzeModel::ROWS_PER_PAGE;
		$config['full_tag_open'] = '<p class="comment-pagination">';
		$config['full_tag_close'] = '</p>';
		$config['uri_segment'] = 4;
		$config['use_page_numbers'] = TRUE;
		$this->pagination->initialize( $config );
		
		return;
	}
}

?>

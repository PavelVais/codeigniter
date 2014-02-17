<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_URI $uri
 * @property CI_DB_active_record $db
 * @property Header $header
 * @property Template $template
 * @property Message $message
 * @property CI_Session $session
 * @property SitemapXMLGenerator $sitemap
 */
class Sitemap extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Vytvoření seznamu stránek pro "strom stránek"
	 */
	function index()
	{
		$this->load->library('sitemapLinkGenerator',null,'sitemap');
		$this->sitemap->set_option('show_index', true); 
		$this->sitemap->ignore('Administration', '*');
		$this->sitemap->ignore('*', array('index','CreateFormSignUp'));
		$this->load->view('sitemap/view_sitemap');
	}

	/**
	 * Funkce na vytvoření sitemapy a následné aktualizace na googlu.
	 * @param String $redirect [false] - neprovede se redirect. Jinak se 
	 * funkce redirectuje na vloženou routu v $redirect
	 */
	public function create($redirect = false)
	{
		$this->load->library( 'sitemapXMLGenerator',null,'sitemap');

		$webs = array("");	//= Vložení stránek, které chce uživatel dát do sitemapy

		foreach ( $webs as $web )
		{
			$item = array(
			    "loc" => site_url( $web ),
			    "lastmod" => date( "c" ),	//date( "c", strtotime( $web->created))
			    "changefreq" => "weekly",
			    "priority" => "1"
			);

			$this->sitemap->add_item( $item );
		}

		// file name may change due to compression
		$file_name = $this->sitemap->build( "sitemap.xml", false );
		$responses = $this->sitemap->ping( site_url( $file_name ) );
		
		if ($redirect != false)
		{
			$this->session->set_flashdata("admin","Obnovení sitemapy proběhlo v pořádku.");
			redirect($redirect);
		}
			

	}

}

/* End of file homepage.php */
/* Location: ./application/controllers/homepage.php */
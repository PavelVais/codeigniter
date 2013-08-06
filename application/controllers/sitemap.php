<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_URI $uri
 * @property CI_DB_active_record $db
 * @property Header $header
 * @property Menu $menu
 * @property Tank_auth $tank_auth //sprava prihlasenych
 * @property Template $template
 * @property Message $message
 * @property MY_Calendar $calendar
 * @property CI_Session $session
 * @property GoogleAnalytics $googleanalytics
 * @property Sitemaps $sitemaps
 */
class Sitemap extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library( 'message' );
	}

	public function nenalezena($title = null)
	{
		set_status_header(404);
		$data['message'] = $this->message->get( Message::ERROR,true );
		if ( !($data['message']) )
			$data['message'] = "Stránka na kterou se snažíte dostat nejspíše neexistuje. :(";
		$nm = new NotificationsModel;
		$data['notification_count'] = $nm->count_notifications();

		if ( $title != null )
			$data['title'] = $title;

		$this->session->keep_flashdata( Message::ERROR );
		$this->load->view( "view_404", $data );
	}

	public function index($redirect = false)
	{
		$this->load->library( 'sitemaps');

		$cm = new ConfessionModel;
		$hm = new HashtagsModel;
		
		$results = $cm->get_old();
		
		foreach($results as &$res)
		{
			$res->url = base_url("confession/".$res->id);
		}
		$this->db->select("created");
		$results2 = $hm->get();
		
		foreach($results2 as &$res)
		{
			
			$res->url = base_url("hashtag/".HashtagsModel::hashtag2url($res->value));
		}
		
		$result = array_merge($results,$results2 );
		
		$webs = array("");

		foreach ( $webs as $web )
		{
			$item = array(
			    "loc" => site_url( $web ),
			    "lastmod" => date( "c" ),
			    "changefreq" => "weekly",
			    "priority" => "1"
			);

			$this->sitemaps->add_item( $item );
		}

		foreach ( $result AS $ress )
		{
			$item = array(
			    "loc" => $ress->url,
			    "lastmod" =>  date( "c", strtotime( isset($ress->created) ? $ress->created : null)),
			    "changefreq" => "daily",
			    "priority" => "0.8"
			);

			$this->sitemaps->add_item( $item );
		}
		// file name may change due to compression
		$file_name = $this->sitemaps->build( "sitemap.xml", false );

		$responses = $this->sitemaps->ping( site_url( $file_name ) );
		
		if ($redirect != false)
		{
			$this->session->set_flashdata("admin","Obnovení sitemapy proběhlo v pořádku.");
			redirect("administrace/nastaveni");
		}
			

	}

}

/* End of file homepage.php */
/* Location: ./application/controllers/homepage.php */
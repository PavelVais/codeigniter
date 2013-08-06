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
 */
class Hashtag extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->helper( 'text' );
	}

	public function index()
	{
		redirect("");
	}
	public function show($hashtag = null)
	{
		$cm = new ConfessionModel();
		$data['hashtag'] = "#" . $hashtag;
		$confessions = $cm->filter( "seen", 1 )->get_by_hashtag( $hashtag );

		$data['confessions'] = $confessions;
		if ( $confessions == FALSE )
		{
			$this->load->view( "hashtag/view_404", $data );
			return;
		}

		$this->load->view( "hashtag/view_show", $data );
	}

	public function add()
	{

		sleep( 1.4 ); //= just for an effect!

		$cm = new ConfessionModel;

		$text = $this->input->post( "txt_confession" );
		$hashtag = $this->input->post( "inp_hashtag" );

		if ( strlne( $text ) < 10 )
		{
			$msg = "<strong>We are sorry!</strong> But your confession is too short.";
			$this->output->json_append( "response", $msg )
					  ->json_append( "status", 500 )
					  ->json_flush();
			return;
		}

		if ( strlen( $text ) > self::CONFESSION_LENGTH )
		{
			$msg = "<strong>We are sorry!</strong> But your confession is too long. Please write it in shorter version.";
			$this->output->json_append( "response", $msg )
					  ->json_append( "status", 500 )
					  ->json_flush();
			return;
		}

		if ( $cm->add( $text, $hashtag ) == FALSE )
		{
			$msg = "<strong>We are sorry!</strong> But someting goes wrong and we cant save your confession.";
			$this->output->json_append( "response", $msg )
					  ->json_append( "status", 500 )
					  ->json_flush();
			return;
		}

		if ( $this->input->is_ajax_request() )
		{
			$msg = "<strong>Thank you!</strong> You have successfully written new confession.";
			$msg .= "<br> Do you want insert <a href='#' id='insert-new'>new one</a>?";
			$this->output->json_append( "response", $msg )
					  ->json_append( "status", 200 )
					  ->json_flush();
		}
		else
		{
			$this->session->set_flashdata( "default", "<strong>Thank you!</strong> You have successfully written new confession." );
			redirect( "" );
		}
	}

}

/* End of file homepage.php */
/* Location: ./application/controllers/homepage.php */
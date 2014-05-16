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
 * @property MY_Lang $lang
 * @property GoogleAnalytics $googleanalytics
 */
class Test extends My_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->helper( 'text' );
	}

	/**
	 *
	 */
	public function index()
	{
		$url = 'http://www.myslimnatebe.cz/At-MyslimNaTebecz-pouziva-co-nejvic-lidi-3xyjFeK';
		$data = array();
		echo $this->get_url_contents( $url );
		$data['url'] = $url;
		$this->load->view( 'dev/js_inject', $data );
	}
	
	public function monitor()
	{
		Head::add()->css('dev/jsmonitor.css');
		$this->load->view('dev/view_jsmonitor');
	}

	/**
	 * @ajax-only
	 */
	public function doAjaxCalling()
	{
		//set POST variables
		
		FB::info( $_POST );
		$url = $_POST['ci_url'];
		unset( $_POST['ci_url'] );

		$post = $this->input->post();

		$fields_string = "";
		//url-ify the data for the POST
		if ( $post != null )
		{
			foreach ( $post as $key => $value )
			{
				$fields_string .= $key . '=' . $value . '&#038;';
			}
			$fields_string = rtrim( $fields_string, '&#038;' );
		}
		//open connection
		$ch = curl_init();

		//set the url, number of POST vars, POST data
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_POST, count( $_POST ) );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $fields_string );
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Requested-With: XMLHttpRequest", "Content-Type: application/json; charset=utf-8"));
		//execute post
		return curl_exec( $ch );

		//close connection
		curl_close( $ch );
	}

	private function get_url_contents($url)
	{
		$crl = curl_init();
		$timeout = 5;
		curl_setopt( $crl, CURLOPT_URL, $url );
		curl_setopt( $crl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $crl, CURLOPT_CONNECTTIMEOUT, $timeout );
		$ret = curl_exec( $crl );
		curl_close( $crl );
		return $ret;
	}

}

/* End of file homepage.php */
/* Location: ./application/controllers/homepage.php */
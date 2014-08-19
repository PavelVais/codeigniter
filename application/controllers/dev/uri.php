<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 */
class Uri extends My_Controller {

	public function __construct() {
		parent::__construct();
	}

	public function index() {

		echo "<H1>TEST Link tridy</H1>";
		\Benchmark\Timer::start();
		echo \URI\Link::URL( '!?form', array("ahoj", "key" => "value") );
		\Benchmark\Timer::mark( "1" );
		echo '<br>';
		echo \URI\Link::URL( '!horn', array("ahoj", "jebat" => "STREJNDA") );
		\Benchmark\Timer::mark( "2" );
		echo '<br>';
		echo \URI\Link::URL( 'dev/form', array("ahoj", "jebat" => "STREJNDA") );
		\Benchmark\Timer::mark( "3" );
		echo '<br>';
		echo \URI\Link::URL( '/form', array("ahoj", "jebat" => "STREJNDA") );
		\Benchmark\Timer::mark( "4" );
		echo '<br>';
		echo \URI\Link::URL( ':example.com', "argument" );
		\Benchmark\Timer::mark( "5" );
		echo '<br>';
		\Benchmark\Timer::stop();
		header();

		//$this->load->view( 'dev/view_ga', $data );
	}

}

/* End of file homepage.php */
/* Location: ./application/controllers/homepage.php */
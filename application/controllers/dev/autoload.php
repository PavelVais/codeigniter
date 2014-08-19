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
 */
class Autoload extends My_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->helper( 'text' );
	}

	function cache()
	{
		\Benchmark\Timer::start();
		
		//= nacitani zbytecnych trid...
		FlashMessage::set("aaa");
		$nav = new Navigation\Navigator();
		\URI\Link::URL("aoj");
		$m = new HTML\Table();
		$h = new Hook\Placer('aaa');
		$dummy = \HTML\Element::open('aa');
		\Benchmark\Timer::stop();
	}

}

/* End of file homepage.php */
/* Location: ./application/controllers/homepage.php */
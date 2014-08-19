<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * 
 */
class Hook extends My_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->helper( 'text' );
	}

	function index()
	{
		\Benchmark\Timer::start();
		$h1 = new \Hook\Placer(  "H1" );
		$h2 = new \Hook\Placer( "H2" );
		$h3 = new \Hook\Placer( "H3" );
		$a = "H4";
		$h4 = new \Hook\Placer( function() use ($a,$h1){
			return $a.' zkouška hooku: '.$h1->getPriority();
		} );

		$h1->setPriority( 58 );
		$h2->setPriority( 78 );
		$h3->setPriority( 5 );
		\Benchmark\Timer::mark( 'after init' );
		$holder = new \Hook\Holder();
		$holder->addHook( $h1 )
				  ->addHook( $h2 )
				  ->addHook( $h3 )
				  ->addHook( $h4 );
		
		\Benchmark\Timer::mark( 'before shuffle' );
		$holder->shuffleByPriority();
		\Benchmark\Timer::mark( 'after shuffle' );

		$str = '';
		while ( ($h = $holder->getHook()) != null )
		{
			$str .= $h . "<br>";
			\Benchmark\Timer::mark( 'loop' );
		}

		\Benchmark\Timer::stop();
		echo $str;
	}

}

/* End of file homepage.php */
/* Location: ./application/controllers/homepage.php */
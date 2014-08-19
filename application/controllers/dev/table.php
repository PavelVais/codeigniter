<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**

 */
class Table extends My_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->helper( 'text' );
	}

	function index()
	{
		\Benchmark\Timer::start();
		$Table = new \HTML\Table;

		
		$rowHook = new \Hook\Placer(function($arg){
			/* @var \HTML\Element $arg */
			$arg[0]->addData('title',$arg[1]);
		});
		
		$data = array(
			 array(
				  \HTML\Table::ROW_CLASS_IDENTIFIER => 'success',
				  'hook' => $rowHook,
				  'id' => 5,
				  'name' => 'Pavel',
				  'prijmeni' => 'Havelka',
				  'ostatni' => array(
						'hook' => new \Hook\Placer(function($args)
						{
							$args[0]->appendString( 'mrdeeeeeat? ' . $args[1]['val'] )
									  ->addAttribute('colspan',2);
						} ),
						'val' => 'pafka'
				  )
			 ),
			 array(
				  'hook' => $rowHook,
				  'id' => 8,
				  'name' => 'Mrzák',
				  'prijmeni' => 'Plzák!!',
				  'ostatni' => array(
						'hook' => new \Hook\Placer(function($args)
						{
							$args[0]->appendString( 'joeefefefefefefefeeeejo? ' . $args[1]['val'] )
									  ->addAttribute('colspan',2);
						} ),
						'val' => 'pafka'
				  )
			 )
		);
		$Table->addRows( $data )
				  ->setHeader( 'id,name,prijmeni,hook,empty column' );
		$echo = $Table->generate();
		\Benchmark\Timer::stop();	
		$data['echo'] = $echo;
		$this->load->view("dev/view_table",$data);
	}
	
	public function tablereader()
	{
		
	}

}

/* End of file homepage.php */
/* Location: ./application/controllers/homepage.php */
<?php

namespace Navigation;

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * Cache Class
 *
 * Partial Caching library for CodeIgniter
 *
 * @category	Libraries
 * @author		Pavel Vais
 * @version		1.0
 */
class Navigator
{

	private $data;

	function __construct()
	{
		$this->add( 'administrace', 'Hlavní strana','fa-dashboard' );
	}

	function add($href, $title, $fontAwesomeIco = null)
	{
		$this->data[] = array(
			 $href, $title, $fontAwesomeIco
		);
		return $this;
	}

	/**
	 * Vypise celou strukturu navigace
	 * @return string
	 */
	function flush()
	{
		$ol = \HTML\Element::open( 'ol' )
				  ->addAttribute( 'class', 'breadcrumb visible-sm visible-md visible-lg' )
				  ->addAttribute( 'style', 'position: absolute; left: 250px; top: 10px;' );
		$i = 0;
		foreach ( $this->data as $href )
		{
			$li  = \HTML\Element::open( 'li' );

			if ( count( $this->data ) == $i + 1 )
			{
				$li->addAttribute( 'class', 'active' )
						  ->appendString( (isset( $href[2] ) == null ? '' : '<i class="fa ' . $href[2] . '"></i> ') . $href[1] );
			}
			else
			{

				$li->append( \HTML\Element::open( 'a' )
									 ->addAttribute( 'href', site_url( $href[0] ) )
									 ->appendString( (isset( $href[2] ) == null ? '' : '<i class="fa ' . $href[2] . '"></i> ') . $href[1] )
				);
			}
			$ol->append( $li, true );
			$i++;
		}
		return $ol->generate();
	}

}

/* End of file Navigator.php */
/* Location: ./application/libraries/components/Navigator.php */
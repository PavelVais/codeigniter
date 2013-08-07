<?php

/**
 * SPL Autoload Helper
 * Elliott Brueggeman
 * http://www.ebrueggeman.com
 */
/* * * nullify any existing autoloads ** */
spl_autoload_register( null, false );

/* * * specify extensions that may be loaded ** */
spl_autoload_extensions( '.php, .class.php' );

/* * * class Loader ** */

function classLoader($class)
{

	$path = APPPATH . 'models/' . strtolower( $class ) . '.php';
	if ( file_exists( $path ) )
	{
		include $path;
		return;
	}

	$models_folders = array(
		 array(
			  'path' => BASEPATH . 'models/',
			  'folders' => array(
			  )
		 ),
		 array(
			  'path' => APPPATH . 'models/',
			  'folders' => array(
					'databasemodel',
					'forms',
					'abstract'
			  )
		 )
	);


	foreach ( $models_folders AS $folder )
	{
		foreach ( $folder['folders'] as $f )
		{
			$path = $folder['path'] . $f . '/' . strtolower( $class ) . '.php';
			if ( file_exists( $path ) )
			{

				include $path;
				return;
			}
		}
	}
}

/* * * register the loader functions ** */
spl_autoload_register( 'classLoader' );

/**
 * Funkce na loadovani trid urceny ke statickym volani funkci.
 * jako $files muze byt i array ve kterem musi byt cesta k souboru.
 * (pokud je v helperu, musi byt 'helper/nazevsouboru')
 * Pokud chcete rovnou volat i nejakou funkci, vlozte nasledujici souhrn atributu
 * [0] = cesta_k_souboru,
 * [1] = nazev_tridy
 * [2] = nazev_funkce (defaultne je "init")
 * @param type $files
 */
function load_static_classes($files)
{
	if ( !is_array( $files ) )
		$files = array($files);

	foreach ( $files as $file )
	{
		$init_class = false;

		if ( is_array( $file ) )
		{
			$init_class = $file[1];
			$init_function = isset( $file[2] ) ? $file[2] : "init";
			$file = $file[0];
		}

		$file = trim( $file, '/' );
		$file = rtrim( $file, '.php' );
		require APPPATH . $file . ".php";

		if ( $init_class != false )
		{
			call_user_func( array($init_class, $init_function) );
		}
	}
}

/* End of file spl_autoload_helper.php */
/* Location: ./system/application/helpers/spl_autoload_helper.php */
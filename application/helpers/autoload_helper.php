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


/* End of file spl_autoload_helper.php */
/* Location: ./system/application/helpers/spl_autoload_helper.php */
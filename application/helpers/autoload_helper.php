<?php

/**
 * SPL Autoload Helper
 * Elliott Brueggeman
 * extended by Pavel Vais
 * http://www.ebrueggeman.com
 */
/* * * nullify any existing autoloads ** */
spl_autoload_register( null, false );

/* * * specify extensions that may be loaded ** */
spl_autoload_extensions( '.php, .class.php' );

/* * * class Loader ** */

class Autoloader
{

	static $folders;

	/**
	 * Init funkce tridy Autoloader
	 * Nacte veskere cesty, do kterych se system diva a prispusobi
	 * spl loader.
	 */
	static function init()
	{
		self::$folders = array(
			 BASEPATH . 'models/',
			 APPPATH . 'models/',
			 APPPATH . 'models/databasemodel',
			 APPPATH . 'libraries/forms/'
		);
		spl_autoload_register( 'Autoloader::load' );

		if ( !class_exists( 'CI_Model' ) )
		{
			load_class( 'Model', 'core' );
		}
	}

	/**
	 * Funkce automaticky volana, pokud se zavola nova instance
	 * jeste nenactene tridy.
	 * @param String $class
	 * @return void
	 */
	static function load($class)
	{
		foreach ( self::$folders AS $folder )
		{
			$path = rtrim( $folder, '/' ) . '/' . strtolower( $class ) . '.php';
			if ( file_exists( $path ) )
			{
				include $path;
				return;
			}
		}
	}

	/**
	 * Nacitani trid, do kterych se ma pristupovat
	 * pomoci statickych metod.<br>
	 * @param type $path - cesta k souboru vcetne nazvu souboru
	 * @param String $class - mozne formy zapisu:
	 * "Trida", "Trida::funkceKteraSeMaZavolat"
	 */
	static function loadStatic($path, $class)
	{
		$file = rtrim( $path, '.php' );
		require_once APPPATH . $path . ".php";

		if ( strpos( $class, "::" ) !== false )
		{
			call_user_func( $class );
		}
	}

}

/* * * register the loader functions ** */
Autoloader::init();

/* End of file spl_autoload_helper.php */
/* Location: ./system/application/helpers/spl_autoload_helper.php */
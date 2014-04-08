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

	/** @var autoloader_mapper */
	static $autoloaderMapper;

	/** @var autoloader_finder */
	static $finder;

	/**

	 * Init funkce tridy Autoloader
	 * Nacte veskere cesty, do kterych se system diva a prispusobi
	 * spl loader.
	 */
	static function init()
	{
		//debug
		require_once APPPATH . '/libraries/components/FirePHPCore/FirePHP.php';
		require_once APPPATH . '/libraries/components/FirePHPCore/fb.php';
		require_once APPPATH . '/libraries/Cache.php';

		self::$folders = array(
			 APPPATH . 'libraries/',
			 APPPATH . 'models/'
		);
		spl_autoload_register( 'Autoloader::load' );

		if ( !class_exists( 'CI_Model' ) )
		{
			load_class( 'Model', 'core' );
		}
		$ci = & get_instance();

		$ci->load->library( 'cache' );
		$mapper = new autoloader_mapper( self::$folders );
		//$mapper->createMap();
		self::$finder = new autoloader_finder( $mapper );
	}

	/**
	 * Funkce automaticky volana, pokud se zavola nova instance
	 * jeste nenactene tridy.
	 * @param String $class
	 * @return void
	 */
	static function load($class)
	{
		if ( strpos( $class, 'CI_' ) !== false )
			return;
		if ( strpos( $class, 'MY_' ) !== false )
			return;

		self::$finder->find( $class );
	}

	

}

class autoloader_mapper
{

	private $paths;

	const MAP_CACHE_FILE = 'atlc/atlc_map';

	private $map;

	public function __construct($paths)
	{
		$this->paths = is_array( $paths ) ? $paths : array($paths);
		$this->map = null;
	}

	/**
	 * Nacist mapu souboru a slozek, ve kterych se ma hledat
	 * @return array
	 */
	public function loadMap()
	{
		$ci = & get_instance();
		$ci->load->library( 'cache' );
		if ( ($cache = $ci->cache->get( self::MAP_CACHE_FILE )) == FALSE )
		{
			$cache = $this->createMap();
		}
		$this->map = $cache;
		return $cache;
	}

	/**
	 * Vytvori mapu souboru a slozek, ve kterych se potom hledaji soubory
	 */
	public function createMap()
	{
		$map = array();
		foreach ( $this->paths as &$path )
		{
			$path = rtrim( $path, '/' );
			$dirs = $this->getDirectories( $path );

			foreach ( $dirs as $dir )
			{
				$map[] = $dir;
			}
		}

		$ci = & get_instance();
		$ci->cache->write( $map, self::MAP_CACHE_FILE, 60 * 60 * 24 * 7 );
		$this->map = $map;
		return $map;
	}

	/**
	 * Vrati mapu souboru a slozek
	 * @return type
	 */
	public function getMap()
	{
		return $this->map == null ? $this->loadMap() : $this->map;
	}

	/**
	 * Vrati vsechny slozky a soubory 
	 * [0] folders -> , files -> ...
	 * @param string $path
	 * @return array
	 */
	private function getDirectories($path)
	{
		$directories = new RecursiveIteratorIterator(
				  new ParentIterator( new RecursiveDirectoryIterator( $path ) ), RecursiveIteratorIterator::SELF_FIRST );

		// Nejdrive zmapujeme root slozku
		$dirs[] = array(
			 'folder' => $path,
			 'files' => $this->getAllFilesFromDir( $path )
		);

		// Nyni zmapujeme vnitrek child slozek
		foreach ( $directories as $directory )
		{
			/* @var $directory SplFileInfo */
			$dirs[] = array(
				 'folder' => $directory->getPathname(),
				 'files' => $this->getAllFilesFromDir( $directory->getPathname() )
			);

			//FB::info( $directory->getPathname() );
		}

		return $dirs;
	}

	/**
	 * Ziska vsechny soubory z dane slozky
	 * @param string $dir
	 * @return array
	 */
	private function getAllFilesFromDir($dir)
	{
		$result = array();
		foreach ( new DirectoryIterator( $dir ) as $fileInfo )
		{
			if ( $fileInfo->isDot() )
				continue;
			if ( $fileInfo->isFile() && $fileInfo->getExtension() == 'php' )
				$result[] = $fileInfo->getFilename();
		}

		return $result;
	}

}

class autoloader_finder
{

	/** @var autoloader_mapper */
	private $mapper;

	/** @var autoloader_file_cache */
	public $cache;

	public function __construct(autoloader_mapper $mapper)
	{
		$this->mapper = $mapper;
		$this->cache = new autoloader_file_cache();
	}

	/**
	 * Najde typ souboru. Pokud ho nenajde v cachi, bude muset hledat v mape souboru a slozek.
	 * Pokud to nanajde ani tam, zkusi znova obnovit mapu souboru a znova najit.
	 * Pokud ani podruhe nenajde, vrati se FALSE
	 * @param string $className
	 * @return boolean
	 */
	public function find($className)
	{
		$className = $this->prepareClassName( $className );
		if ( ($final_path = $this->cache->getPath( $className->fullname )) !== FALSE )
		{
			include $final_path;
			return TRUE;
		}

		if ( $this->cache->isBanned( $className->fullname ) )
		{
			return FALSE;
		}

		return $this->deepFind( $className );
	}

	/**
	 * Hleda v mape souboru a slozek (autoload mapper)
	 * @staticvar boolean $renew
	 * @param string $filename
	 * @return boolean
	 */
	private function deepFind($filename)
	{
		static $renew;
		$map = $this->mapper->getMap();
		foreach ( $map as $dir )
		{
			if ( ($path = $this->isExists( $dir, $filename->name ) ) )
			{
				if ( is_bool( $this->setPriority( $path, $filename ) ) )
				{
					// Ulozime cestu do cache (jedna se o nonnamespace soubor)
					$this->cache->put( $filename->fullname, $path );
					include $path;
					return;
				}
			}
		}
		if ( !empty( $filename->paths ) )
		{
			$this->includeByPriority( $filename );
			return true;
		}

		if ( $renew == null )
		{
			$renew = true;
			//FB::info( "soubor $filename->fullname se nenašel, jdu to znova oscanovat.. co kdyby nahodou.." );
			$this->mapper->createMap();
			
			return $this->deepFind( $filename );
		}

		//= Soubor se nanasel ani na podruhy, asi se nacita jinak
		//= takze ho dam do banlistu
		//FB::info( "soubor $filename->fullname nebyl ani na podruhe nalezen, zabanuji ho." );
		$this->cache->ban( $filename->name );
		return false;
	}

	private function includeByPriority($fileClass)
	{
		//FB::info( "Includuji soubor " . $fileClass->name );
		if ( count( $fileClass->paths ) > 1 )
		{
			usort( $fileClass->paths, function($a, $b)
			{
				return $a['priority'] < $b['priority'];
			} );
			//FB::info( 'includuji nejvetsi prioritu: ' . $fileClass->paths[0]['path'] );
		}
		$this->cache->put( $fileClass->fullname, $fileClass->paths[0]['path'] );
		include $fileClass->paths[0]['path'];
	}

	private function setPriority($path, &$fileClass)
	{
		$namespaces = $fileClass->namespaces;
		if ( empty( $namespaces ) )
		{
			$fileClass->paths[] = array(
				 'path' => $path,
				 'priority' => 0);
			return true;
		}
		$path_parts = pathinfo( $path );
		$dirs = explode( '/', $path_parts['dirname'] );
		$priority = 0;
		foreach ( $dirs as $dir )
		{
			foreach ( $namespaces as $nm )
			{
				if ( strtolower( $dir ) == strtolower( $nm ) )
				{
					$priority += 1;
				}
			}
		}
		$fileClass->paths[] = array(
			 'path' => $path,
			 'priority' => $priority
		);
		/*FB::group( "priorita" );
		FB::info( $priority, 'priorita' );
		FB::info( $dir, 'cesta' );
		FB::info( $fileClass, 'soubor' );
		FB::groupEnd();*/

		return $fileClass;
	}

	private function prepareClassName($className)
	{
		// let's find namespaces!
		$result = explode( '\\', $className );

		if ( count( $result ) == 1 )
		{
			$r = new stdClass();
			$r->name = $result[0];
			$r->namespaceID = '';
			$r->namespaces = array();
			$r->fullname = $r->name;
			$r->paths = array();
			return $r;
		}
		else
		{
			$nm = array_splice( $result, 0, count( $result ) - 1 );
			$r = new stdClass();
			$r->name = $result[count( $result ) - 1];
			$r->namespaceID = implode( '', $nm );
			$r->namespaces = $nm;
			$r->fullname = $r->namespaceID . $r->name;
			$r->paths = array();
			return $r;
		}
	}

	/**
	 * Existuje dany soubor? nerozlisuje velikost pismen! oujeee!
	 * @param array $dirContent
	 * @param string $filename
	 * @return boolean
	 */
	private function isExists($dirContent, $filename)
	{
		foreach ( $dirContent['files'] as $file )
		{
			//FB::info( $file . ' == ' . $filename . ' -> ' . (strtolower( $file ) == strtolower( $filename ) . '.php' && file_exists( $dirContent['folder'] . '/' . $file ) ? 'ANO' : 'NE'), 'isExists()' );
			if ( strtolower( $file ) == strtolower( $filename ) . '.php' && file_exists( $dirContent['folder'] . '/' . $file ) )
			{
				return $dirContent['folder'] . '/' . $file;
			}
		}

		return false;
	}

}

class autoloader_file_cache
{

	private $map;
	private $unsavedMap;
	private $banlist;
	private $saveBans;

	const FILES_CACHE_NAME = 'atlc/atlc_names';

	public function __construct()
	{
		$this->unsavedMap = array();
		$this->saveBans = false;

		$ci = & get_instance();
		if ( (list($this->map, $this->banlist) = $ci->cache->get( self::FILES_CACHE_NAME )) == FALSE )
		{
			$this->map = array();
			$this->banlist = array();
		}
		/*FB::group( "file cache" );
		FB::info( $this->map, "file map" );
		FB::info( $this->banlist, "banlist" );
		FB::groupEnd();*/
	}

	public function renew()
	{
		$ci = & get_instance();
		$ci->cache->delete( self::FILES_CACHE_NAME );
		$this->map = array();
		$this->banlist = array();
		return $this;
	}

	/**
	 * Ziska cestu, ktera je pridruzena k danemu souboru
	 * @param type $className
	 * @return boolean
	 */
	public function getPath($className)
	{
		if ( empty( $this->map ) )
		{
			return false;
		}
		$hash = self::hashFilename( $className );

		//FB::info( $className . ' - ' . $hash . ' - ' . (isset( $this->map[$hash] ) ? 'yes' : 'no'), 'getPath()' );

		return isset( $this->map[$hash] ) ? $this->map[$hash] : false;
	}

	public function put($filename, $path)
	{
		$this->unsavedMap[self::hashFilename( $filename )] = $path;
	}

	/**
	 * Soubor, ktery nechcete hledat (nacita ho jiny autoloader napr.)
	 * se pres tuto funkci hodi do banu, a tim pri dalsim loadingu se nenacte.
	 * @param string $filename
	 */
	public function ban($filename)
	{
		$this->saveBans = true;
		$this->banlist[] = $filename;
	}
	
	/**
	 * Z blacklistu vyhodí soubor, ktery nechcete, aby byl nadále blokován.
	 * Cela cache se automaticky ulozi
	 * @param string $filename
	 */
	public function unBan($filename)
	{
		$this->saveBans = true;
		foreach ( $this->banlist as $key => $ban )
		{
			if ( strtolower( $ban ) == strtolower( $filename ) )
				unset($this->banlist[$key]);
		}
		$this->commit();
	}

	public function isBanned($filename)
	{
		foreach ( $this->banlist as $ban )
		{
			if ( strtolower( $ban ) == strtolower( $filename ) )
				return TRUE;
		}
		return FALSE;
	}

	public function commit($overwrite = false)
	{
		if ( empty( $this->unsavedMap ) && !$this->saveBans )
		{
			//FB::info( 'ATCL Cache commit() nebylo nic uloženo.' );
			return $this->map;
		}

		if ( !empty( $this->map ) && !$overwrite )
		{
			$this->unsavedMap = array_merge( $this->map, $this->unsavedMap );
		}

		/*FB::info( $this->unsavedMap, "TCL Cache commit() - Ukladam cesty k souborum" );
		FB::info( $this->banlist, "TCL Cache commit() - Ukladam cesty k souborum" );*/
		$ci = & get_instance();
		$ci->cache->write( array($this->unsavedMap, $this->banlist), self::FILES_CACHE_NAME, 60 * 60 * 24 * 7 );
		$this->map = $this->unsavedMap;
		return $this->map;
	}

	static function hashFilename($path)
	{
		return md5( $path );
	}

}


/* * * register the loader functions ** */
Autoloader::init();

/* End of file spl_autoload_helper.php */
/* Location: ./system/application/helpers/spl_autoload_helper.php */
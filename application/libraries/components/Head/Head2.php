<?php

namespace Head;

/**
 * Description of Head2
 * Head2::addJS();
 * @author Vais
 */
class Head2 {

	private static $ci;
	private static $container;
	private static $settings;

	/**
	 * Oznacuje stav, pri kterem je hlavicka vykreslena a pouze se muze 
	 * vykreslit deferred objekty
	 * @var int 
	 * 0: nic neni vypsano
	 * 1: non-deferred jsou vypsany
	 * 2: i deferred objekty jsou vypsany
	 */
	public static $state = 0;
	private static $init_loaded = false;
	private static $production_refresh = false;

	/**
	 * Instance tridy Head
	 * @var Head2 
	 */
	static $singleton;

	static public function init() {
		self::$ci = & get_instance();
		self::$singleton = new self;
		self::$ci->load->config( 'head' );
		self::$ci->lang->load( 'common' );
		self::$settings = self::$ci->config->item( 'header' );
		self::$settings['canonical'] = current_url();
	}

	/**
	 * Generate whole head object. If you call it twice, you will create
	 * deferred object (second call must be at the end of page)
	 * @param string $title
	 * @param boolean $close
	 */
	static function generate($title, $close = true) {
		//= Something is really happening!! Let's load additional stuff
		self::$singleton->init_config();

		if ( self::$state === 0 ) {
			//= Opravdu se neco generuje! Hura nacist dodatecne soubory a dalsi veci
			self::addString( '<link href="' . base_url() . self::get_setting( 'favicon' ) . '" rel="icon" type="image/x-icon">' );
			self::$settings['title'] = $title;

			echo self::get_setting( "doctype" ) . PHP_EOL;
			echo "<html lang=" . self::get_setting( 'language' ) . '>' . PHP_EOL;
			echo "<head>" . PHP_EOL;
			echo '<meta charset=' . self::get_setting( 'encode' ) . '>' . PHP_EOL;
			echo '<link rel="canonical" href="' . self::$settings['canonical'] . '">' . PHP_EOL;
		}

		if ( ENVIRONMENT == 'production' ) {
			self::$singleton->productionGenerate();
		}
		else {
			self::$singleton->generateObjects(
				   self::$singleton->filter( 'deferred', self::$state !== 0 ) );
		}

		if ( self::$state === 0 )
			echo '<title>' . ( self::get_setting( 'title' ) == "" ? self::get_setting( 'title-postfix' ) : self::get_setting( 'title' ) . self::get_setting( 'title-union' ) . self::get_setting( 'title-postfix' )) . '</title>' . PHP_EOL;

		if ( $close && self::$state === 0 )
			self::close();

		self::$state = self::$state === 0 ? 1 : 2;
	}

	/**
	 * Generate deferred objects
	 */
	static function generateDeferred() {
		if ( ENVIRONMENT == 'production' ) {
			self::$singleton->productionGenerate();
		}
		else {
			self::$singleton->generateObjects(
				   self::$singleton->filter( 'deferred', true ) );
		}
		self::$state = 2;
	}

	/**
	 * Ostatni parametry se nastavuji v configu (jsou nedinamicke) 
	 * @param string $title - titulek
	 * @param string $image_url - image url
	 * @param string $site_url - canonicka url, pokud neni vyplnena, pouzije se
	 * jiz stavajici canonicka url
	 */
	static public function facebook_metas($title, $image_url, $description = null, $site_url = null) {
		$data = new Common_Object();
		$data->meta_type = 'property';
		$data->meta_type_value = 'og:image';
		$data->meta_content = $image_url;
		self::addMeta( $data );

		$data = new Common_Object();
		$data->meta_type = 'property';
		$data->meta_type_value = 'og:title';
		$data->meta_content = $title;
		self::addMeta( $data );

		if ( $site_url == null )
			$site_url = self::$settings['canonical'];

		$data = new Common_Object();
		$data->meta_type = 'property';
		$data->meta_type_value = 'og:url';
		$data->meta_content = $site_url;
		self::addMeta( $data );

		if ( $description !== null ) {
			$data = new Common_Object();
			$data->meta_type = 'property';
			$data->meta_type_value = 'og:description';
			$data->meta_content = strip_tags( $description );
			self::addMeta( $data );
		}
	}

	/**
	 * This function just close head. Wow, isn't it?
	 */
	static public function close() {
		echo '</head>' . PHP_EOL;
	}

	/**
	 * Ziska nastaveni z nacteneho configu. V priklade moznosti danou hodnotu
	 * prelozit, ji prelozi
	 * @param string $setting
	 * @return string
	 */
	static private function get_setting($setting) {
		if ( self::$settings['use_lang_file'] ) {
			if ( in_array( $setting, array('doctype', 'keywords', 'language', 'title-postfix') ) ) {
				return self::$ci->lang->line( self::$settings[$setting] );
			}
		}
		return self::$settings[$setting];
	}

	public function __construct() {
		
	}

	private function init_config() {
		if ( self::$init_loaded )
			return;
		self::$init_loaded = true;

		self::$container['META'] = array();
		self::$container['CSS'] = array();
		self::$container['JS'] = array();
		self::$container['STRING'] = array();
		self::$container['VIEW'] = array();

		self::$ci->load->driver( 'minify' );

		//= add META elements from config file
		if ( isset( self::$settings['meta'] ) )
			foreach ( self::$settings['meta'] as $data )
			{
				$this->addData( $this->array2CommonObj( $data, Common_Object::TYPE_METADATA ), Common_Object::TYPE_METADATA );
			}
		//= add CSS elements from config file
		if ( isset( self::$settings['css'] ) )
			foreach ( self::$settings['css'] as $data )
			{
				$this->addData( $this->array2CommonObj( $data, Common_Object::TYPE_CSS ), Common_Object::TYPE_CSS );
			}

		//= add JS elements from config file
		if ( isset( self::$settings['js'] ) )
			foreach ( self::$settings['js'] as $data )
			{
				$this->addData( $this->array2CommonObj( $data, Common_Object::TYPE_JAVASCRIPT ), Common_Object::TYPE_JAVASCRIPT );
			}
		//= add STRING elements from config file
		if ( isset( self::$settings['string'] ) )
			foreach ( self::$settings['string'] as $data )
			{
				$this->addData( $this->array2CommonObj( $data, Common_Object::TYPE_STRING ), Common_Object::TYPE_STRING );
			}
		//= add VIEW elements from config file
		if ( isset( self::$settings['view'] ) )
			foreach ( self::$settings['view'] as $data )
			{
				$this->addData( $this->array2CommonObj( $data, Common_Object::TYPE_VIEW ), Common_Object::TYPE_VIEW );
			}
	}

	static function setCanonicalURL($url) {
		self::$settings['canonical'] = $url;
	}

	/**
	 * Prida JS kod do hlavicky (nebo na spod jako deferred object)
	 * @param Common_Object / string $data - muze se jednat o URL adresu
	 * @return Common_Object
	 */
	static function &addJS($data) {
		return self::$singleton->addData( $data, Common_Object::TYPE_JAVASCRIPT );
	}

	/**
	 * Prida CSS kod do hlavicky (nebo na spod jako deferred object)
	 * @param Common_Object / string $data - muze se jednat o URL adresu
	 * @return Common_Object
	 */
	static function &addCSS($data) {
		return self::$singleton->addData( $data, Common_Object::TYPE_CSS );
	}

	static function &addMeta($data) {
		return self::$singleton->addData( $data, Common_Object::TYPE_METADATA );
	}

	/**
	 * 
	 * @param string / \Head\Common_Object $data
	 * - muze byt pouze string, nebo kompletni head common object.
	 * @return Common_Object
	 */
	static function &addString($data) {
		return self::$singleton->addData( $data, Common_Object::TYPE_STRING );
	}

	/**
	 * 
	 * @param string / \Head\Common_Object $data
	 * @return Common_Object
	 */
	static function &addView($data) {
		return self::$singleton->addData( $data, Common_Object::TYPE_VIEW );
	}

	/**
	 * Vyfiltruje prvky dle atributu a jeho hodnoty
	 * @param string $attribute
	 * @param string $value
	 * @param string $type - Pokud je urcen, filtruji se obbjekty
	 * pouze z dane kategorie (css,js,...)
	 * @param boolean $extract - vyjme prvky z kontejneru 
	 * @return array
	 */
	static function filter($attribute, $value, $type = null, $extract = false) {
		$return = array();
		foreach ( self::$container as $K => &$types )
		{
			if ( $type !== null && $K !== $type )
				continue;

			foreach ( $types as $key => $data )
			{
				if ( $data->{$attribute} == $value ) {
					$return[] = $data;
					if ( $extract )
						unset( $types[$key] );
				}
			}
			unset( $types );
		}
		return $return;
	}

	/**
	 * Vrati prvek s referenci, takze jde dal upravovat.
	 * Pozor: Prvky, ktere nesplnuji cover nebo splnuji except podminku
	 * v systemu nadale neexistuji a proto nejdou vratit!!
	 * @param string $type - JS, CSS, META, STRING, VIEW
	 * @param string $term - termin, dle ktereho vyselektuje PRVNI pouzitelny prvek
	 * @param boolean $byCacheName - [false:default] - hledat dle URL
	 * [true] - hledat dle parametru cacheName (name)
	 * @return Common_Object / null
	 */
	static function &getElement($type, $term, $byCacheName = false) {
		foreach ( self::$container[$type] as &$data )
		{
			if ( (!$byCacheName && in_array( $url, $data->url )) || ($byCacheName && $data->cacheName == $url) ) {
				return $data;
			}
		}

		return null;
	}

	/**
	 * Prida data do systemu. toto je privatni funkce, ktera 
	 * je vyuzivana funkcemi: addJS, addCSS atd.
	 * @param string / \Head\Common_Object $data
	 * @param string $type
	 * @return Common_Object
	 */
	private function &addData($data, $type) {
		if ( is_string( $data ) ) {
			$data = new Common_Object( $data, $type );
			$data->type = $type;
		}
		else if ( is_array( $data ) ) {
			$data = self::$singleton->array2CommonObj( $data, $type );
			$data->type = $type;
		}
		else {
			$data->type = $type;
		}
		//\FB::info($data->url,'url');
		//\FB::info(self::$singleton->validate( $data ),'a');
		//\FB::info(self::$container,'container');
		if ( !self::$singleton->validate( $data ) ) {
			$c = new Common_Object();
			return $c;
		}

		//= Paklize se prida kod jiz po vypsani hlavicky,
		//= automaticky se nastavi jako deferred
		if ( self::$state === 1 )
			$data->deferred = true;

		if ( !self::$init_loaded ) {
			self::$singleton->init_config();
		}


		self::$container[$data->type][] = $data;
		return self::$container[$data->type][count( self::$container[$data->type] ) - 1];
	}

	private function productionGenerate() {
		if ( self::$state === 0 ) {
			$result = self::$container[Common_Object::TYPE_CSS];
			if ( !empty( $result ) ) {
				$result = self::$singleton->mergeObjects( $result );
				$result->minify = true;
				$result->cacheName = $result->getUniqueID();
				$result->version = '1.0';
				$result->debug = self::$production_refresh ||
					   self::$singleton->compare_files( $result->url, 'css/' . $result->getCacheName( self::$settings['cache-css-prefix'] ), 'css/' );
				self::$singleton->generateCSS( $result );
			}
			self::$container[Common_Object::TYPE_CSS] = array();
		}
		$result = self::$singleton->filter( 'deferred', self::$state !== 0, Common_Object::TYPE_JAVASCRIPT, true );

		//= Pokud je co, tak to vygenerujeme (nejdrive vsak sjednotime)
		if ( !empty( $result ) ) {
			$result = self::$singleton->mergeObjects( $result );
			$result->minify = true;
			$result->cacheName = $result->getUniqueID();
			$result->version = '1.0';
			$result->debug = self::$production_refresh ||
				   self::$singleton->compare_files( $result->url, 'js/' . $result->getCacheName( self::$settings['cache-js-prefix'] ), 'js/' );
			self::$singleton->generateJS( $result );
		}
		self::$singleton->generateObjects(
			   self::$singleton->filter( 'deferred', self::$state !== 0 ) );

		return $this;
	}

	/**
	 * Oznaci produkcni balicky jako debugove , tzn je pri kazdem refreshi prepise a znova
	 * zminimalizuje.
	 */
	static function productionRefresh() {
		self::$production_refresh = true;
		/* self::$singleton->init_config();
		  $result = self::$container[Common_Object::TYPE_CSS];
		  \FB::info( $result, 'a' );
		  if ( !empty( $result ) )
		  {
		  $result = self::$singleton->mergeObjects( $result );
		  $result->minify = true;
		  $result->cacheName = $result->getUniqueID();
		  $result->version = '1.0';
		  \FB::info( $result, 'vymazávám cache objektu' );
		  self::cacheDelete( $result );
		  }
		  $result = self::$singleton->filter( 'deferred', false, Common_Object::TYPE_JAVASCRIPT, true );
		  if ( !empty( $result ) )
		  {
		  $result = self::$singleton->mergeObjects( $result );
		  $result->minify = true;
		  $result->cacheName = $result->getUniqueID();
		  $result->version = '1.0';
		  \FB::info( $result, 'vymazávám cache objektu' );
		  self::cacheDelete( $result );
		  }
		  $result = self::$singleton->filter( 'deferred', true, Common_Object::TYPE_JAVASCRIPT, true );
		  if ( !empty( $result ) )
		  {
		  $result = self::$singleton->mergeObjects( $result );
		  $result->minify = true;
		  $result->cacheName = $result->getUniqueID();
		  $result->version = '1.0';
		  \FB::info( $result, 'vymazávám cache objektu' );
		  self::cacheDelete( $result );
		  } */
	}

	/**
	 * 
	 * @param \Head\Common_Object $file - Pokud je urcen,
	 * smaze pouze dotycny objekt, ktery je ulozen jako cache.
	 * V druhem pripade vymaze vscehny CSS a JS cache soubory
	 */
	static function cacheDelete(Common_Object $file = null) {
		if ( $file !== null ) {
			$e = 'css';
			if ( $file->type == Common_Object::TYPE_JAVASCRIPT ) {
				$e = 'js/cache';
			}
			$n = $e . '/' . $file->getCacheName( self::$settings['cache-css-prefix'] );
			//\FB::info( $n, 'file_path' );
			//\FB::info( file_exists( $n ), 'file_exists' );
			if ( file_exists( $n ) ) {
				return unlink( $n );
			}
			return false;
		}
		else {
			foreach ( self::$container['JS'] as $data )
			{
				/* @var $data Common_Object */
				$n = 'js/cache/' . $data->getCacheName( self::$settings['cache-js-prefix'] );
				if ( file_exists( $n ) )
					unlink( $n );
			}
			foreach ( self::$container['CSS'] as $data )
			{
				/* @var $data Common_Object */
				$n = 'css/' . $data->getCacheName( self::$settings['cache-css-prefix'] );
				if ( file_exists( $n ) )
					unlink( $n );
			}
		}
	}

	private function generateObjects($objects) {
		foreach ( $objects as $object )
		{
			self::$singleton->{'generate' . $object->type}( $object );
		}
	}

	/**
	 * Predela prvky z head configu do Common Objectu
	 * @param type $array
	 * @param type $type
	 */
	private function array2CommonObj($array, $type) {
		if ( !is_array( $array ) ) {
			$obj = new Common_Object( $array, $type );
		}
		else {
			$obj = new Common_Object( null, $type );
			if ( isset( $array['cover'] ) )
				$obj->coverURI = $array['cover'];
			if ( isset( $array['except'] ) )
				$obj->exceptURI = $array['except'];
			if ( isset( $array['language'] ) )
				$obj->language = $array['language'];
			if ( isset( $array['compress'] ) )
				$obj->minify = $array['compress'];
			if ( isset( $array['name'] ) )
				$obj->cacheName = $array['name'];
			if ( isset( $array['version'] ) )
				$obj->version = $array['version'];
			if ( isset( $array['deferred'] ) )
				$obj->deferred = $array['deferred'];
			if ( isset( $array['detection'] ) )
				$obj->detection = $array['detection'];
			if ( isset( $array['debug'] ) )
				$obj->debug = $array['debug'];
			if ( isset( $array['except'] ) )
				$obj->exceptURI = $array['except'];
			if ( isset( $array['localhost'] ) )
				$obj->localhost = $array['localhost'];

			if ( isset( $array['url'] ) )
				$obj->addURL( $array['url'] );
		}
		if ( $type == 'META' ) {
			$obj->meta_type = isset( $array['property'] ) ? Common_Object::META_TYPE_PROPERTY : (isset( $array['name'] ) ? Common_Object::META_TYPE_NAME : Common_Object::META_TYPE_HTTP_EQUIV);
			$obj->meta_type_value = $array[$obj->meta_type];
			$obj->meta_content = $array['content'];
		}
		return $obj;
	}

	/**
	 * Sjednoti vice objektu do jednoho
	 * @param array $objects
	 * @return Common_Object
	 */
	private function mergeObjects($objects) {

		/* @var $finalObj Common_Object */
		$finalObj = array_shift( $objects );
		foreach ( $objects as $object )
		{
			/* @var $object Common_Object */
			$finalObj->addURL( $object->url );

			if ( $finalObj->cacheName === '' && $object->cacheName !== '' ) {
				$finalObj->cacheName = $object->cacheName;
			}
		}
		$finalObj->version = '';
		$finalObj->cacheName = md5( $finalObj->cacheName );
		return $finalObj;
	}

	/**
	 * Generovani Javascript kodu
	 * @param \Head\Common_Object $object
	 * @return \Head\Head2
	 */
	private function generateJS(Common_Object $object) {

		if ( $object->minify ) {
			$url = $object->getCacheName( self::$settings['cache-js-prefix'] );
			if ( $object->debug || !file_exists( 'js/' . $url ) ) {
				foreach ( $object->url as &$u )
				{
					$u = Common_Object::is_url_external( $u ) ? $u : 'js/' . $u;
				}
				\FB::info( $url, 'generuji novou cache JS' );
				$min_output = self::$ci->minify->combine_files( $object->url, "js", TRUE );
				self::$ci->minify->save_file( $min_output, 'js/' . $url );
			}
			echo '<script src="' . base_url( 'js/' . $url ) . '"></script>' . PHP_EOL;

			return $this;
		}

		foreach ( $object->url as $url )
		{
			echo '<script src="' . (Common_Object::is_url_external( $url ) ? $url : base_url( 'js/' . $url ) ) . '"></script>' . PHP_EOL;
		}

		return $this;
	}

	/**
	 * Generovani CSS kodu
	 * @param \Head\Common_Object $object
	 * @return \Head\Head2
	 */
	private function generateCSS(Common_Object $object) {
		if ( $object->minify ) {
			$url = $object->getCacheName( self::$settings['cache-css-prefix'] );
			if ( $object->debug || !file_exists( 'css/' . $url ) ) {

				foreach ( $object->url as &$u )
				{
					$u = 'css/' . $u;
				}
				\FB::info( $url, 'generuji novou cache CSS' );
				\FB::info( $object->url, 'jeji url' );
				$min_output = self::$ci->minify->combine_files( $object->url, "css", TRUE );
				self::$ci->minify->save_file( $min_output, 'css/' . $url );
			}

			echo '<link rel="stylesheet" type="text/css" href="' . base_url( 'css/' . $url ) . '">' . PHP_EOL;
			return $this;
		}

		foreach ( $object->url as $url )
		{
			echo '<link rel="stylesheet" type="text/css" href="' . (Common_Object::is_url_external( $url ) ? $url : base_url( 'css/' . $url ) ) . '">' . PHP_EOL;
		}
		return $this;
	}

	/**
	 * Generovani View kodu
	 * @param \Head\Common_Object $object
	 * @return \Head\Head2
	 */
	private function generateVIEW(Common_Object $object) {

		foreach ( $object->url as $url )
		{
			echo self::$ci->load->view( $url, null, TRUE ) . PHP_EOL;
		}

		return $this;
	}

	/**
	 * Generovani View kodu
	 * @param \Head\Common_Object $object
	 * @return \Head\Head2
	 */
	private function generateSTRING(Common_Object $object) {

		foreach ( $object->url as $url )
		{
			echo $url . PHP_EOL;
		}

		return $this;
	}

	/**
	 * Generovani Meta kodu
	 * @param \Head\Common_Object $object
	 * @return \Head\Head2
	 */
	private function generateMETA(Common_Object $object) {
		echo '<meta ' . $object->meta_type . '="' . $object->meta_type_value . '" content="' . $object->meta_content . '">' . PHP_EOL;
		return $this;
	}

	/**
	 * Zvaliduje vlozeny objekt a dle jeho parametru a aktualni
	 * url adresy zjisti, jestli se ma vlozit (true), nebo ne (false)
	 * @param \Head\Common_Object $object
	 * @return boolean
	 */
	public function validate(Common_Object $object) {

		//= Zadne validace nejsou potreba
		if ( $object->coverURI == null && $object->exceptURI == null && $object->language == null && $object->localhost === null ) {
			return TRUE;
		}

		//= Je urcen jazyk, pro ktery se maji data aplikovat?
		if ( $object->language != null && self::$ci->lang->lang() !== $object->language ) {
			return FALSE;
		}
		if ( $object->localhost !== null ) {
			if ( IS_LOCALHOST !== $object->localhost ) {
				return false;
			}
		}

		// V teto chvili musime zkontrolovat, jestli COVER souhlasi a EXCEPT ne
		if ( $object->coverURI == 'none' )
			return FALSE;

		$good = true;
		if ( $object->coverURI !== null )
			$good = $this->is_in_url( $object->coverURI );

		if ( $object->exceptURI !== null )
			$good = !$this->is_in_url( $object->exceptURI ) && $good;
		
		return $good;
	}

	/**
	 * Zjisti, zdali retezec nebo array je obsazen v URL adrese
	 * @param type $rules
	 * @return boolean 
	 */
	private function is_in_url($rules) {
		if ( !is_array( $rules ) ) {
			$rules = array($rules);
		}

		foreach ( $rules AS $singl )
		{
			if ( is_array( $singl ) ) {
				if ( self::$ci->uri->segment( $singl[1] ) == $singl[0] ) {
					return true;
				}
			}
			else {
				$segmentCount = substr_count( $singl, '/' );
				$total = self::$ci->uri->total_segments();
				for ( $index = 1; $index <= $total; $index++ )
				{
					$temp = '';

					if ( $index + $segmentCount > $total )
						break;
					//= Buildovani url adresy shodne s cover adresou
					for ( $f = $index; $f <= $index + $segmentCount; $f++ )
					{
						if ( $f == $index + $segmentCount ) {
							$temp .= self::$ci->uri->segment( $f );
						}
						else {
							$temp .= self::$ci->uri->slash_segment( $f );
						}
					}

					if ( $temp == $singl ) {
						return true;
					}
				}
			}
		}
		return false;
	}

	private function compare_files($files, $tofile, $filesPrefix = '') {

		// flag to check if any of the file was changed to rebuild all the set of files
		if ( !is_array( $files ) ) {
			$files = (array) $files;
		}
		//\FB::info( $tofile, 'tofile' );
		if ( file_exists( $tofile ) ) {
			$x = filemtime( $tofile );
		}
		else {
			$x = 0;
		}
		//\FB::info( $x, 'x' );
		foreach ( $files as $j )
		{
			$filename = $filesPrefix . $j;
			//\FB::info( filemtime( $filename ) . ' > ' . $x, 'compare (' . $filename . ')' );
			if ( file_exists( $filename ) && filemtime( $filename ) > $x ) {
				return true;
			}
		}
		return false;
	}

}

class Common_Object {

	const TYPE_JAVASCRIPT = 'JS';
	const TYPE_CSS = 'CSS';
	const TYPE_VIEW = 'VIEW';
	const TYPE_METADATA = 'META';
	const TYPE_STRING = 'STRING';
	const META_TYPE_NAME = 'name';
	const META_TYPE_PROPERTY = 'property';
	const META_TYPE_HTTP_EQUIV = 'http-equiv';

	public $deferred;
	public $url = array();
	public $data;
	public $type;
	public $language = null;
	public $minify;
	public $meta_type;
	public $meta_type_value;
	public $meta_content;
	public $detection;

	/**
	 * Nazev cache souboru, ktery se vytvori po minifikaci a sjednoceni 
	 * elementu. (CSS a JS only)
	 * @var string 
	 */
	public $cacheName = '';

	/**
	 * Script se bude vzdy kompilovat a nebude cekat na zmenu verze
	 * @var boolean 
	 */
	public $debug = false;

	/**
	 * Urcuje verzi objektu (vetsinou JS).
	 * Pokud se zmeni verze, prekompiluje se JS a pote ulozi
	 * @var string
	 */
	public $version = '1.0';

	/**
	 * casti URI, ktere musi byt v URL adresse,
	 * aby se dany objekt vypsal
	 * @var string/array
	 */
	public $coverURI = null;

	/**
	 * casti URI, ktere nesmi byt v URL adresse,
	 * aby se dany objekt vypsal
	 * @var string/array
	 */
	public $exceptURI = null;

	/**
	 * Pokud je TRUE, prvek se vypise pouze pri pristupu z localhostu,
	 * pokud je FALSE, prvek se vypise pouze pokud NENI z localhostu,
	 * pokud je NULL, tento prvek se ignoruje
	 * @var boolean 
	 */
	public $localhost = null;

	public function __construct($url = null, $type = self::TYPE_JAVASCRIPT, $deferred = false) {
		$this->type = $type;
		if ( $url != null ) {
			$this->addURL( $url );
		}

		$this->deferred = $deferred;
	}

	/**
	 * Prida url adresu / adresy do daneho prvku
	 * @param string / array $url
	 * @return \Head\Common_Object
	 */
	public function addURL($url) {

		if ( !is_array( $url ) ) {
			$this->url[] = $url;
		}
		else {
			$this->url = array_merge( $this->url, $url );
		}
		return $this;
	}

	static function is_url_external($url) {
		return (strpos( $url, 'htt' ) === 0);
	}

	public function getCacheName($prefix) {
		return $prefix . $this->cacheName . $this->version . '.' . strtolower( $this->type );
	}

	/**
	 * Vygeneruje unikatni identifikator, ktery se vytvori dle
	 * vlozenych URL adres
	 * @return string
	 */
	public function getUniqueID() {
		return md5( implode( '', $this->url ) );
	}

}

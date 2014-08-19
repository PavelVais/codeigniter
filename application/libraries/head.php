<?php
/**
 * @deprecated since version 1.0
 */
class Head
{

	private static $ci;
	private static $container;
	private static $settings;

	/**
	 * Urcuje, jestli se ma vypsat jiz odlozena scripta,stringu a view (true)
	 * nebo se ma vypsat vse, krom odlozenych script,stringu a view
	 * @var boolean
	 */
	private static $generate_deffered;

	static public function init()
	{
		self::$ci = & get_instance();
		self::$ci->load->helper( "html" );
		self::$ci->load->config( 'head' );
		self::$ci->lang->load( 'common' );

		self::$settings = self::$ci->config->item( 'header' );
		self::$generate_deffered = false;
		
		//= Na konci initu spravime vsechny tagy v configu, aby se nahrali
		self::config_tags_proceed();
	}

	static public function generate($title = null, $close = true)
	{
		if ( !self::$generate_deffered )
		{
			//= Opravdu se neco generuje! Hura nacist dodatecne soubory a dalsi veci
			self::add()->string( '<link href="' . base_url() . self::get_setting( 'favicon' ) . '" rel="icon" type="image/x-icon">' );
			self::$settings['title'] = $title;

			echo doctype( self::get_setting( "doctype" ) ) . PHP_EOL;
			echo "<html lang=" . self::get_setting( 'language' ) . '>' . PHP_EOL;
			echo "<head>" . PHP_EOL;
			echo '<meta charset=' . self::get_setting( 'encode' ) . '>' . PHP_EOL;
			echo link_tag( current_url(), 'canonical', 'none' ) . PHP_EOL;
		}

		self::print_tags( "meta" );
		self::print_tags( "css" );
		self::print_tags( "js" );

		if ( !self::$generate_deffered )
			echo '<title>' . ( self::get_setting( 'title' ) == "" ? self::get_setting( 'title-postfix' ) : self::get_setting( 'title' ) . self::get_setting( 'title-union' ) . self::get_setting( 'title-postfix' )) . '</title>' . PHP_EOL;

		self::print_tags( "string" );
		self::print_tags( "view" );

		if ( $close && !self::$generate_deffered )
			self::close();

		self::$generate_deffered = true;
	}

	static public function close()
	{
		echo '</head>' . PHP_EOL;
	}

	static private function get_setting($setting)
	{
		if ( self::$settings['use_lang_file'] )
			if ( $setting == 'doctype' || $setting == 'keywords' || $setting == 'language' || $setting == 'title-postfix' )
				return (isset( self::$settings[$setting] ) ? self::$ci->lang->line( self::$settings[$setting] ) : false);
		return (isset( self::$settings[$setting] ) ? self::$settings[$setting] : false);
	}

	static private function config_tags_proceed()
	{
		$head_factory = new Head_factory( self::$settings );

		$fnc = function($obj, $type, Head_factory $head_factory)
				  {
					  if ( !is_array( $obj ) )
						  return;

					  foreach ( $obj as $value )
					  {
						  $head_factory->{$type}( $value );
					  }
				  };

		$fnc( self::get_setting( "meta" ), "meta", $head_factory );
		$fnc( self::get_setting( "css" ), "css", $head_factory );
		$fnc( self::get_setting( "js" ), 'js', $head_factory );
		$fnc( self::get_setting( "string" ), 'string', $head_factory );
		$fnc( self::get_setting( "view" ), 'view', $head_factory );
	}

	static private function print_tags($type)
	{

		if ( !isset( self::$container[$type] ) )
			return false;

		foreach ( self::$container[$type] as $tag )
		{
			if ( (self::$generate_deffered && $tag['deferred']) || (!self::$generate_deffered && !$tag['deferred']) )
			{
				echo $tag['value'];
			}
		}
	}

	static public function remove()
	{
		return new Head_factory( self::$settings, Head_factory::MODE_REMOVE );
	}

	static public function add()
	{
		return new Head_factory( self::$settings );
	}

	static public function add_to_container($type, $identifier, $string, $deffered = false)
	{
		self::$container[$type][$identifier] = array(
			 "deferred" => $deffered,
			 "value" => $string
		);
	}

	static public function remove_from_container($type, $identifier = null)
	{
		if ( $identifier == null )
			unset( self::$container[$type] );
		unset( self::$container[$type][$identifier] );
	}

}

class Head_factory
{
	private $ci;
	private $mode_add;

	const MODE_ADD = true;
	const MODE_REMOVE = false;

	public function __construct($settings, $mode = self::MODE_ADD)
	{
		$this->mode_add = $mode;
		$this->ci = & get_instance();
		$this->cssPrefix = $settings['cache-css-prefix'];
		$this->jsPrefix = $settings['cache-js-prefix'];
	}

	/**
	 * Vygeneruje meta znacku.
	 * @param array $data. Strutura musi byt:
	 * ['name'] OR ['property'] OR ['http-equiv'] => "nazev meta znacky"
	 * ['content'] => "hodnota meta znacky"
	 * @return \Head_factory
	 */
	public function meta($data = null)
	{
		if ( !$this->mode_add )
		{
			Head::remove_from_container( 'meta', $data );
			return $this;
		}
		if ( !$this->validator( $data ) )
			return $this;

		$type = isset( $data['name'] ) ? 'name' : (isset( $data['property'] ) ? 'property' : 'http-equiv');
		$val = $data[$type];

		$data = $this->builder( "meta", array(
			 $type => $data[$type],
			 'content' => $data['content']
				  ) );

		Head::add_to_container( "meta", $val, $data );
		return $this;
	}

	/**
	 * Prida CSS tag, muze se vlozit jak samotna url, tak i cele pole
	 * @param string / array $data
	 * @return \Head_factory
	 */
	function css($data = null)
	{
		$is_array = is_array( $data );
		$urls = !$is_array ? $data : (array_key_exists( 'url', $data ) ? $data['url'] : $data);
		if ( !$this->mode_add )
		{
			//= Pokud se jedna o vymazani, tak se data vymazou a tim script konci
			Head::remove_from_container( 'css', $data == null ? null : $urls  );
			return $this;
		}

		if ( !$this->validator( $data ) )
			return $this;
		//= Musime url adresu pretvorit na pole
		if ( !is_array( $urls ) )
			$urls = array($urls);

		$identifier = $urls[0]; //= Identifikator, ktery slouzi k pripadnemu vymazani
		//= Nejedna se nahodou o cachovani?
		if ( $is_array && isset( $data['compress'] ) && $data['compress'] )
		{
			$filename = $data['name'] . $data['version'] . ".css";
			$urls = $this->cssPrefix . $filename;
			if ( ($min_output = $this->ci->minify->get_file( "css/" . $urls )) == FALSE || (isset( $data['debug'] ) && $data['debug']) )
			{
				$min_output = $this->ci->minify->combine_files( array_map( function($val)
									 {
										 return 'css/' . $val;
									 }, $data['url'] ), "css", TRUE );
				$this->ci->minify->save_file( $min_output, "css/" . $urls );
			}

			$identifier = $data['name'];
			$urls = array($urls);
		}

		foreach ( $urls as $url )
		{
			$identifier = $url;
			//= Kontrola, jestli je adresa interni ci externi
			$parts = parse_url( $url );
			if ( !isset( $parts['scheme'] ) )
				$url = site_url( "css/" . $url );

			$data = $this->builder( "link", array(
				 'rel' => "stylesheet",
				 'type' => 'text/css',
				 'href' => $url
					  ) );

			Head::add_to_container( "css", $identifier, $data );
		}
		return $this;
	}

	/**
	 * Prida JS tag, muze se vlozit jak samotna url, tak i cele pole
	 * @param string / array $data
	 * @return \Head_factory
	 */
	function js($data = null)
	{
		$deferred = false;
		$is_array = is_array( $data );
		$urls = !$is_array ? $data : $data['url'];
		$compress = false;
		if ( !$this->mode_add )
		{
			Head::remove_from_container( 'js', $data == null ? null : $urls  );
			return $this;
		}

		if ( !$this->validator( $data ) )
			return $this;

		if ( $is_array )
			$deferred = isset( $data['deferred'] ) && $data['deferred'];



		if ( !is_array( $urls ) )
			$urls = array($urls);

		$identifier = $urls[0];

		if ( $is_array && isset( $data['compress'] ) && $data['compress'] )
		{
			$compress = true;
			$filename = $data['name'] . $data['version'] . ".js";
			$urls = $this->jsPrefix . $filename;
			if ( ($min_output = $this->ci->minify->get_file( 'js/' . $urls )) == FALSE || (isset( $data['debug'] ) && $data['debug']) )
			{
				$min_output = $this->ci->minify->combine_files( $data['url'], "js", TRUE );
				$this->ci->minify->save_file( $min_output, 'js/' . $urls );
			}
			$identifier = $data['name'];
			$urls = array($urls);
		}

		foreach ( $urls as $url )
		{
			//= Kontrola, jestli je adresa interni ci externi
			$parts = parse_url( $url );
			if ( !isset( $parts['scheme'] ) )
				$url = site_url( "js/" . $url );

			$data = $this->builder( "script", array('src' => $url), true );
			Head::add_to_container( "js", $compress ? $identifier : $url, $data, $deferred );
		}
		return $this;
	}

	function string($string)
	{
		if ( !$this->mode_add )
		{
			Head::remove_from_container( 'string', $string );
			return $this;
		}

		Head::add_to_container( "string", $string, $string . PHP_EOL );
		return $this;
	}

	function view($view_path)
	{

		if ( !$this->validator( $view_path ) )
			return $this;

		$view_path = !is_array( $view_path ) ? $view_path : $view_path['url'];

		if ( !$this->mode_add )
		{
			Head::remove_from_container( 'view', $view_path );
			return $this;
		}
		Head::add_to_container( "view", $view_path, $this->ci->load->view( $view_path, null, TRUE ) . PHP_EOL );
		return $this;
	}

	private function validator($obj)
	{
		//= pokud neni pole, je jasne, ze se musi dany objekt pouzit na kazde strance
		if ( !is_array( $obj ) )
			return TRUE;

		//= Je urcen jazyk, pro ktery se maji data aplikovat?
		if ( isset( $obj['language'] ) && $this->ci->lang->lang() != $obj['language'] )
		{
			return FALSE;
		}

		if ( isset( $obj['cover'] ) && $obj['cover'] != 'all' )
		{
			if ( $obj['cover'] == 'none' )
				return FALSE;

			if ( $this->is_in_url( $obj['cover'] ) )
			{
				if ( !$this->is_in_url( isset( $obj['except'] ) ? $obj['except'] : null  ) )
				{
					return TRUE;
				}
			}
			return FALSE;
		}

		if ( isset( $obj['except'] ) )
		{
			if ( !$this->is_in_url( $obj['except'] ) )
				return TRUE;
			else
				return FALSE;
		}
		return TRUE;
	}

	/**
	 * Zjisti, zdali retezec nebo array je obsazen v URL adrese
	 * @param type $rules
	 * @return boolean 
	 */
	private function is_in_url($rules)
	{
		if ( !is_array( $rules ) )
		{
			$rules = array($rules);
		}

		foreach ( $rules AS $singl )
		{
			if ( is_array( $singl ) )
			{
				if ( $this->ci->uri->segment( $singl[1] ) == $singl[0] )
				{
					return true;
				}
			}
			else
			{
				$segmentCount = substr_count( $singl, '/' );
				for ( $index = 1; $index <= $this->ci->uri->total_segments(); $index++ )
				{
					$temp = '';

					if ( $index + $segmentCount > $this->ci->uri->total_segments() )
						break;
					//= Buildovani url adresy shodne s cover adresou
					for ( $f = $index; $f <= $index + $segmentCount; $f++ )
					{
						if ( $f == $index + $segmentCount )
						{
							$temp .= $this->ci->uri->segment( $f );
						}
						else
						{
							$temp .= $this->ci->uri->slash_segment( $f );
						}
					}

					if ( $temp == $singl )
					{
						return true;
					}
				}
			}
		}
		return false;
	}

	private function builder($tag_name, $attributes, $close_tag = false)
	{
		$str = '';

		if ( !is_array( $attributes ) )
			$attributes[$attributes] = null;

		foreach ( $attributes as $key => $value )
		{
			$str .= ' ' . $key . ($value === null ? '' : '="' . $value . '"' );
		}
		return '<' . $tag_name . $str . '>' . ($close_tag ? '</' . $tag_name . '>' : '') . PHP_EOL;
	}

}
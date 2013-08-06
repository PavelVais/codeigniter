<?php

/**
 * Trida Header umoznuje generovani automatickych <head> hlaviček v html jazyce
 * Vsechno generovani je ponechano v conf/header.php.
 * Pokud potrebujete neco pridat v prubehu kodu,
 * vola se prikad $header->addParam($typ,$obsah);
 *
 * @todo Prepsat nazvy prikazu dle aktualniho zvyku
 * @version 2.3
 * @author Pavel Vais
 * verze 2.3:
 *  - Pridano cachovani JS a CSS souboru
 * 	 - Pridan parametr defer u JS. zavolani defer souboru se provede generate_deferred()
 * verze 2.2:
 *  - pridany lepsi popisky k funkcim
 *  - url konecne podporuje array! k jednomu objektu muzeme nyni mit vice url
 *  - vymazany zbytecne soubory v comp/header
 *  - promazany zbytecne promenne, jine prosli upravou v nazvu
 * bugfixes 2.1:
 *  - Do Hooku lze vkladat argumenty
 *  - Zprovoznena addParam funkce
 *  - Header neni zavysly na likner helperu, paklize neni system postaven
 * 		na vice domen
 *  - Includovani JS souboru bez koncovky funguje spravne
 *  - Vypisovani title znacky
 * @property Minify $minify
 */
class Header
{

	/**
	 * Cesta k hookum
	 * @var String (path) 
	 */
	private $hooks_folder = 'comp/header/hooks/';

	/**
	 * Urcuje, zdali se ma pri dalsim cyklu uzavrit html podminka
	 * @var boolean 
	 */
	private $condition_open = false;

	/**
	 * Enkodovani stranky. defaultne je utf-8
	 * @var String 
	 */
	private $encode = 'utf-8';

	/**
	 * Autor stranek
	 * @var String 
	 */
	private $author;

	/**
	 * Klicova slova na strankach
	 * @var String
	 */
	private $keywords;

	/**
	 * Popis stranek
	 * @var String 
	 */
	private $description;

	/**
	 * Wrapper, ktery v sobe obsahuje vsechny
	 * dodatecne informace (css,js,meta tagy..)
	 * @var Array 
	 */
	private $content = array();

	/**
	 * Zjisti, jestli je definovano vice domen v danem frameworku
	 * defaultne je FALSE
	 * @var boolean 
	 */
	private $domain_defined = FALSE;

	/**
	 * U techto parametru se kontroluje, jestli jsou obsazeny v configu headeru (config/header.php)
	 * @var type 
	 */
	private $optionsValues = array('author', 'description', 'keywords', 'encode', 'language', 'doctype');

	/**
	 * 
	 * Hlavni konstruktor
	 */
	function __construct()
	{

		$this->ci = & get_instance();
		require SYSDIR.'/helpers/html_helper.php';
		$this->ci->load->helper( 'html' );
		$this->ci->load->config( 'header' );
		$this->ci->load->driver( 'minify' );
		$this->content = $this->ci->config->item( 'header' );

		//= Zjisti, zdali ma prostredi vice domen
		$this->domain_defined = defined( 'DOMAIN' ) ? TRUE : FALSE;

		//= Kontrola, jestli jsou vsechna nastaveni nastavena.
		$this->checkOptions( $this->content, $this->optionsValues );

		$this->keywords = $this->getParam( 'keywords' );
		$this->author = $this->getParam( 'author' );
		$this->description = $this->getParam( 'description' );
	}

	/**
	 * Vygeneruje html hlavicku.
	 * @param String $title
	 * @param boolean $close_head - ma se na konec vlozit uzavreni hlavicky?
	 *  - standartne nastaveno na TRUE
	 */
	public function generate($title = null, $close_head = TRUE)
	{
		if ( $title != null )
			$this->setParam( 'title', $title );

		echo doctype( $this->getParam( 'doctype' ) ) . "\n";
		echo "<html lang=" . $this->getParam( 'language' ) . ">\n";
		echo "<head>\n";
		echo "<meta charset='UTF-8'>\n";
		echo link_tag( site_url(), 'canonical', 'none' ) . "\n";
		echo meta( 'description', $this->description );
		echo meta( 'author', $this->author );
		echo meta( 'keywords', $this->keywords );
		echo '<link href="' . base_url() . '/' . $this->getParam( 'favicon' ) . '" rel="shortcut icon">' . "\n";

		$this->generateObject( 'meta' );
		$this->generateObject( 'link' );
		$this->generateObject( 'css' );
		$this->generateObject( 'js' );
		echo '<title>' . ($this->getParam( 'title' ) == "" ? $this->getParam( 'title-postfix' ) : $this->getParam( 'title' ) . $this->getParam( 'title-union' ) . $this->getParam( 'title-postfix' )) . "</title>\n";
		$this->generateObject( 'hooks' );

		if ( $close_head )
			$this->closeHead();
	}

	private function getHTMLCondition($object)
	{
		if ( $this->condition_open == TRUE )
		{
			$this->condition_open = false;
			return "<![endif]-->\n";
		}

		if ( !isset( $object['condition'] ) )
			return false;

		$ieType = "";
		$ieVersion = "";
		preg_match( '/^ie([0-9]{0,2})([^ ]*)$/', $object['condition'], $conditions );
		if ( count( $conditions ) == 0 )
			return false;

		if ( isset( $conditions[1] ) && $conditions[1] != '' )
			$ieVersion = ' ' . $conditions[1];


		if ( isset( $conditions[2] ) )
		{
			if ( $conditions[2] == 'under' || $conditions[2] == 'below' || $conditions[2] == 'lower' )
				$ieType = ' lte';
			else if ( $conditions[2] == 'up' || $conditions[2] == 'above' )
				$ieType = ' gte';
		}

		$this->condition_open = true;
		echo "<!--[if$ieType IE$ieVersion]>\n";
		return true;
	}

	/**
	 * Zjisti, jestli je dana adresa externi, nebo interni
	 * @param String $string
	 * @return boolean 
	 */
	private function isRemoteUrl($string)
	{
		$parts = parse_url( $string );
		return isset( $parts['scheme'] ) ? true : false;
	}

	/**
	 * Otevre JS script dle konkretniho doctype
	 * @param String $url Pokud se vlozi i url, otevre se javascript rovnou
	 * s url parametrem
	 */
	public function openJSScript($url = null)
	{
		if ( $this->getParam( 'doctype' ) == "html5" )
			echo "<script" . ($url != null ? " src=\"$url\"" : "") . ">";
		else
			echo "<script " . ($url != null ? "src=\"$url\"" : "") . "type='text/javascript' charset='UTF-8'>";
	}

	/**
	 * Uzavre hlavicku
	 */
	public function closeHead()
	{
		echo "\n</head>\n";
	}

	/**
	 * Zavola hook (view), ktery je ulozen ve slozce comp/header/hooks/.
	 * Pokud jsou definovany dalsi argumenty, do view se prevedou v promenynch
	 * arg1 az argX kde X je pocet argumentu
	 * @param String $func_name 
	 * 
	 */
	public function call_hook($func_name)
	{

		$hooks = $this->getParam( 'hooks' );

		$args = func_get_args();
		$i = 0;
		$data = array();
		foreach ( $args as $arg )
		{
			$i++;
			$data['arg' . $i] = $arg;
		}

		if ( $hooks != null )
		{
			foreach ( $hooks AS $h )
			{
				if ( isset( $h['name'] ) && $h['name'] == $func_name )
				{
					$this->ci->load->view( $this->hooks_folder . $h['name'], $data );
				}
				else if ( isset( $h['func_name'] ) && $h['func_name'] == $func_name )
				{
					$this->ci->load->view( $this->hooks_folder . $h['name'], $data );
				}
			}
		}
	}

	/**
	 * Vygeneruje objekty dle danych pravidel pro jednotlive typy<br>
	 * @param String $type
	 * @param String $deffered - TRUE = vygeneruje jen ODLOZENE JS scripta
	 * @return String 
	 */
	private function generateObject($type, $deferred = FALSE)
	{
		$type = strtolower( $type );
		if ( $this->getParam( $type ) == null )
		{
			return;
		}

		foreach ( $this->getParam( $type ) AS $obj )
		{
			//dump('generuji',$type, $this->getParam( $type ) );
			if ( $this->validation( $obj ) == TRUE )
			{
				//dump('validaci prosel: ',$obj,$type);
				switch (strtolower( $type ))
				{
					case 'css':

						if ( !is_array( $obj ) || !isset( $obj['url'] ) )
						{
							$obj = array(
								 'url' => $obj,
								 'media' => 'all'
							);
						};

						$obj['media'] = ( isset( $obj['printable'] ) && $obj['printable'] == TRUE ) ? 'print' : 'all';

						if ( !is_array( $obj['url'] ) )
						{
							$obj['url'] = array($obj['url']);
						}
						$href = array();
						foreach ( $obj['url'] as $url )
						{
							$nameParts = pathinfo( $url );
							$href[] = $this->getLink( 'CSS', $url, isset( $nameParts['extension'] ) ? $nameParts['extension'] : null  );
						}

						break;

					case 'js':
						if ( is_array($obj) && isset( $obj['defer'] ) && $obj['defer'] && !$deferred )
							continue;

						if ( (!isset( $obj['defer'] ) || !$obj['defer']) && $deferred )
							continue;

						if ( !is_array( $obj ) )
						{
							$obj = array('url' => $obj);
						}
						if ( !is_array( $obj['url'] ) )
						{
							$obj['url'] = array($obj['url']);
						}
						$href = array();
						foreach ( $obj['url'] as $url )
						{
							$nameParts = pathinfo( $url );
							$href[] = $this->getLink( 'JS', $url, isset( $nameParts['extension'] ) ? $nameParts['extension'] : null  );
						}

						break;
					case 'links':
					case 'link':

						$l = array(
							 'href' => $obj['url'],
							 'rel' => $obj['rel'],
							 'type' => $obj['type'],
						);
						if ( isset( $obj['media'] ) )
							$l = array_merge( $l, array('media' => $obj['media']) );
						if ( isset( $obj['title'] ) )
							$l = array_merge( $l, array('title' => $obj['title']) );
						break;
					default:
						break;
				}

				$this->getHTMLCondition( $obj );
				switch (strtolower( $type ))
				{
					default:
						break;
					case 'css':
						if ( !is_array( $href ) )
							$href = array($href);
						foreach ( $href as $hf )
						{
							echo link_tag( $hf, 'stylesheet', 'text/css', null, $obj['media'] ) . "\n";
						}
						break;

					case 'js':

						if ( isset( $obj['defer'] ) && $obj['defer'] && !$deferred )
							continue;

						if ( (!isset( $obj['defer'] ) || !$obj['defer']) && $deferred )
							continue;


						if ( !is_array( $href ) )
							$href = array($href);
						foreach ( $href as $hf )
						{
							echo $this->openJSScript( $hf ) . "</script>\n";
						}
						break;

					case "links":
					case 'link':
						echo link_tag( $l ) . "\n";
						break;

					case 'meta':
						echo meta( array('name' => (isset( $obj['name'] ) ? $obj['name'] : $obj['property']), 'content' => $obj['content'], 'type' => (isset( $obj['name'] ) ? 'name' : 'property')) );
						break;

					case 'hooks':
						if ( isset( $obj['included'] ) && $obj['included'] == FALSE )
							continue;

						if ( isset( $obj['arguments'] ) )
						{
							if ( !is_array( $obj['arguments'] ) )
							{
								$obj['arguments'] = array($obj['arguments']);
							}
							$this->ci->load->view( $this->hooks_folder . $obj['name'], $obj['arguments'] );
						}
						else
							$this->ci->load->view( $this->hooks_folder . $obj['name'] );
						break;
				}
				if ( $this->condition_open == true )
					echo $this->getHTMLCondition( $obj );
			}
		}

		if ( $type == "js" || $type == "css" )
			$this->_get_cache_output( $type );

		return;
	}

	public function generate_deferred()
	{
		$this->generateObject( "js", TRUE );
		$this->_get_cache_output( "js", TRUE );
	}

	/**
	 * Podiva se po cover a exclude parametru a z toho zjisti, zdali na
	 * dane strance, muze byt dany objekt pouzit (TRUE)
	 * @param Array $obj
	 * @return boolean 
	 */
	private function validation($obj)
	{
		//= pokud neni pole, je jasne, ze se musi dany objekt pouzit na kazde strance
		if ( !is_array( $obj ) )
			return TRUE;

		if ( isset( $obj['restriction'] ) )
		{
			$r = $this->getParam( 'restriction', array('name' => $obj['restriction']) );

			if ( is_null( $r ) )
				show_error( 'HEADER CLASS: function validation(): restrikce ' . $obj['restriction'] . ' nebyla nalezena.' );

			if ( isset( $r['cover'] ) )
			{
				$obj['cover'] = $r['cover'];
			}
			if ( isset( $r['except'] ) )
			{
				$obj['except'] = $r['except'];
			}
		}
		if ( isset( $obj['cover'] ) && $obj['cover'] != 'all' )
		{
			if ( $obj['cover'] == 'none' )
				return FALSE;

			if ( $this->isInUrl( $obj['cover'] ) )
			{
				if ( !$this->isInUrl( isset( $obj['except'] ) ? $obj['except'] : null  ) )
				{
					return TRUE;
				}
			}
			return FALSE;
		}

		if ( isset( $obj['except'] ) )
		{
			if ( !$this->isInUrl( $obj['except'] ) )
				return TRUE;
			else
				return FALSE;
		}
		return TRUE;
	}

	/**
	 * Vrati spravnou adresu k css / js souborum.
	 * Bere ohled na domeny
	 * @param type $path
	 */
	private function getLink($type, $url, $extension)
	{
		if ( $this->isRemoteUrl( $url ) )
			return $url;

		if ( $this->domain_defined )
		{
			return linker( $type, $url . (!is_null( $extension ) ? "" : '.' . strtolower( $type )) );
		}
		else
		{
			return site_url( strtolower( $type ) . '/' . $url . (!is_null( $extension ) ? "" : '.' . strtolower( $type )) );
		}
	}

	/**
	 * Zjisti, zdali retezec nebo array je obsazen v URL adrese
	 * @param type $rules
	 * @return boolean 
	 */
	private function isInUrl($rules)
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

	/**
	 * Ulozi parametr
	 */
	private function setParam($param, $value)
	{
		$this->content[$param] = $value;
	}

	/**
	 * Prida do pole $where typu $param hodnotu $what.
	 * Pokud je udan $where parametr, muze se tim prepsat jiz udana hodnota.
	 * Pokud nic prepisovat nechcete, $where neuvadejte<br>
	 * pr.: addParam("css","new_css_style")<br>
	 * nebo addParam("css",array(
	 * 		"url" => something,
	 * 		"cover" => something
	 * 
	 * @param String $param
	 * @param String/Array $what 
	 * @param String $where
	 */
	public function addParam($param, $what, $where = null)
	{
		if ( $where == null )
		{
			$this->content[$param][] = $what;
		}
		else
		{
			$this->content[$param][$where][] = $what;
		}
	}

	public function removeObject($type, $name)
	{
		switch (strtolower( $type ))
		{
			case 'js':
				$this->deleteFromParam( $param, $where, $what );
		}
	}

	public function remove_all_data($param)
	{
		$this->content[$param] = array();
		return $this;
	}

	/**
	 * Vymaze dil parametru, vhodne pro CSS / JS
	 * @param type $param
	 * @param type $where
	 * @param type $what
	 */
	private function deleteFromParam($param, $what, $where = null)
	{
		$w = $this->getParam( $param );

		if ( isset( $w[$where] ) )
			return;

		foreach ( $w[$where] AS $key => $value )
		{
			if ( isset( $value ) )
				if ( $value == $what )
				{
					unset( $this->content[$param][$where] );
					$this->content[$param][$where] = array_values( $this->content[$param][$where] );
					return;
				}
		}
	}

	/**
	 * Ziska parametry z velkeho array listu
	 * @param String $param - css, js, meta ...
	 * @param type $filter
	 * @return objekt 
	 */
	private function getParam($param, $filter = null)
	{
		if ( isset( $this->content[$param] ) )
		{
			if ( $filter != null && is_array( $this->content[$param] ) )
			{
				$objs = array();
				foreach ( $this->content[$param] AS $value )
				{
					if ( (!is_array( $filter ) && isset( $value['url'] )) || (is_array( $filter ) && isset( $value[key( $filter )] ) && $value[key( $filter )] == $filter[key( $filter )] ) )
					{
						$objs[] = $value;
					}
				}
				if ( count( $objs ) == 0 )
					return null;
				else
				{
					if ( count( $objs ) == 1 )
					{
						$objs = $objs[0];
					}
				}
				return $objs;
			}
			else
			{
				return $this->content[$param];
			}
		}
		else
		{
			return null;
		}
	}

	private function _get_cache_output($type, $deferred = FALSE)
	{

		$caches = $this->getParam( "caching" );

		if ( $caches !== null )
			foreach ( $caches as $cache )
			{
				if ( $this->validation( $cache ) === FALSE )
					continue;
			
				if ( isset( $cache['defer'] ) && $cache['defer'] && !$deferred )
					continue;

				if ( (!isset( $cache['defer'] ) || !$cache['defer']) && $deferred )
					{
					dump(!isset( $cache['defer'] ),$cache['defer'],$deferred );
							dump($cache);
							continue;
						}

				if ( isset( $cache['js'] ) && $type == "js" )
				{
					$filename = $cache['name'] . $cache['version'] . ".js";
					$path = "js/cache/minjsoutput_" . $filename;
					if ( ($min_output = $this->ci->minify->get_file( $path )) == FALSE || (isset($cache['debug']) && $cache['debug'])  )
					{
						$min_output = $this->ci->minify->combine_files( $cache['js'], "js", TRUE );
						$this->ci->minify->save_file( $min_output, $path );
					}
					echo $this->openJSScript( base_url( $path ) ) . "</script>\n";
				}
				elseif ( isset( $cache['css'] ) && $type == "css" )
				{
					$filename = $cache['name'] . $cache['version'] . ".css";
					$path = "css/cache/minjsoutput_" . $filename;
					if ( ($min_output = $this->ci->minify->get_file( $path )) == FALSE || (isset($cache['debug']) && $cache['debug']) )
					{
						$min_output = $this->ci->minify->combine_files( $cache['css'], "css", TRUE );
						$this->ci->minify->save_file( $min_output, $path );
					}
					echo link_tag( base_url( $path ), 'stylesheet', 'text/css', null, "all" ) . "\n";
				}
			}
	}

	/**
	 * Kontroluje jestli jsou v config/header.php vsechny nalezitosti
	 * @param type $options
	 * @param type $parameters
	 * @return boolean 
	 */
	private function checkOptions($options, $parameters)
	{
		if ( $options == null )
			show_error( 'HEADER CLASS: function checkOptions: $options argument je nulový!' );
		if ( !is_array( $options ) )
			show_error( 'HEADER CLASS: function checkOptions: $options argument není pole!' );

		$countOpt = count( $options );
		$f = 0;

		foreach ( $parameters AS $opt )
		{
			$f = 0;
			foreach ( $options AS $par => $value )
			{
				$f++;
				if ( $opt == $par )
					break;
				if ( ($f + 1) == $countOpt )
				{
					show_error( 'HEADER CLASS: function checkOptions: V nastavení (config/header.php) není definováno ' . $opt . ' !' );
				}
			}
		}
		return true;
	}

	/**
	 * Nastavi encodovani stranky (utf-8)
	 * @param String $encode
	 */
	public function setEncode($encode)
	{
		$this->encode = $encode;
	}

	/**
	 * Nastavi autora stranky
	 * @param String $author
	 */
	public function setAuthor($author)
	{
		$this->author = $author;
	}

	/**
	 * Nastavi klicova slova na strance
	 * @param String $keywords
	 */
	public function setKeywords($keywords)
	{
		$this->keywords = $keywords;
	}

	/**
	 * Nastavi popisek stranky
	 * @param String $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}

	/**
	 * Prida automaticky text za titulek
	 * @param String $title_postfix 
	 */
	public function setTitlePostfix($title_postfix)
	{
		$this->setParam( "title-postfix", $title_postfix );
	}

}


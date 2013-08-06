<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_URI $uri
 * @property Cache $cache
 */
class Template
{

	/**
	 * 
	 * @var array
	 */
	private $templates;
	private $current_template;
	private $tags;
	private $expiration;

	/**
	 * polozka v poli templates u kazdeho templatu
	 * Tagy urcujici o jakou cache se jedna
	 * @var String 
	 */

	const TAGS = 'tags';

	/**
	 * Expirace cache v minutach
	 * @var int
	 */
	const EXPIRATION = 'expiration';

	/**
	 * polozka v poli templates u kazdeho templatu
	 * Obsahuje cachovany html kod
	 */
	const VIEW = 'view';

	/**
	 * obsahuje nazev templatu
	 */
	const NAME = 'name';

	/**
	 * pul denni expirace
	 * @var int 
	 */
	const EXP_12_HOURS = 720;

	/**
	 * Denni expirace
	 * @var int
	 */
	const EXP_DAY = 1440;

	/**
	 * Tydenni expirace
	 * @var int 
	 */
	const EXP_7_DAYS = 10080;

	/**
	 * Mesicni expirace
	 * @var int
	 */
	const EXP_MONTH = 43200;

	/**
	 * Expirace v tomto pripade neprobehne
	 * @var int 
	 */
	const EXP_NO_EXPIRATION = null;

	private $cache;

	/**
	 * Pokud se pres funkci disable_caching nastavi na false,
	 * prikaz load se vzdy vykona bez cachovani
	 * @var type 
	 */
	private $caching_enabled = TRUE;

	function __construct()
	{

		if ( !is_dir( APPPATH . "/views/templates/" ) )
		{
			show_error( "Trida Template musi mit slozku view/templates." );
		}

		$this->ci = & get_instance();
		$this->ci->load->library( 'cache' );
		$this->cache = $this->ci->cache;

		$this->templates[] = array(
			 self::EXPIRATION => self::EXP_DAY,
			 self::TAGS => null,
			 self::NAME => null,
			 self::VIEW => null
		);

		$this->current_template = & $this->templates[0];
	}

	/**
	 * Invaliduje dany template
	 * (predtim se upresni pres self::tags)
	 * @return \Template 
	 */
	public function invalide($template_name)
	{
		if ( $this->get_template_index_by_name( $template_name ) < 0 )
		{
			//= jedna se o prvni template	
			$this->set_name( $template_name );
		}
		else
		{
			$this->set_current_template( $template_name );
		}
		$this->cache->delete( $this->build_cache_name() );
		return $this;
	}

	/**
	 * Nacte sablonu. Pokud je cachovana, nacte se ze souboru
	 * a parametr $data nevyuzije. V druhem pripade se nacte znovu
	 * ze slozky templates
	 * @param type $name - nazev sablony (fyzickeho souboru)
	 * @param type $data - data, ktere se do sablony prenesou
	 * @return \Template 
	 */
	public function load($name, $data = null)
	{
		$this->current_template[self::NAME] = $name;
		$cached_result = false;
		
		if ( !$this->caching_enabled )
		{
			$result = $this->ci->load->view( "templates/" . $name, $data, TRUE );
		}
		else
		{
			$cached_result = $this->cache->get( $this->build_cache_name() );
			if ( $cached_result == false )
			{
				$result = $this->ci->load->view( "templates/" . $name, $data, TRUE );
			}
			else
			{
				$result = $cached_result;
				unset( $data );
			}
		}
		$index = $this->get_template_index_by_name( $name ); //= existuje dany template uz nebo ne?

		if ( $index > 0 )
		{
			$this->set_current_template( $index );
		}


		$this->current_template[self::VIEW] = $result;

		//= Pokud dany template jeste neeixstuje, zapiseme ho do seznamu
		if ( $index < 0 )
		{
			$this->templates[] = $this->current_template;
			//= nacteme navaznost na nove vznikle pole
			$this->set_current_template( count( $this->templates ) - 1 );
		}

		//= jako posledni krok zapiseme cache (pokud neni)
		if ( $cached_result == false && $this->caching_enabled)
		{
			$this->cache->write( $result, $this->build_cache_name(), $this->current_template[self::EXPIRATION] );
		}


		return $this;
	}

	/**
	 * nastavi expiraci u aktivniho templatu
	 * @param int $expiration
	 * @return \Template 
	 */
	public function set_expiration($expiration = self::EXP_DAY)
	{
		$this->current_template[self::EXPIRATION] = $expiration;
		return $this;
	}

	/**
	 * Nastavi aktualni sablonu, na kterou se budou aplikovavat
	 * ostatni funkce
	 * @param int/String $index_name - nazev sablony nebo index pozice sablony
	 * @return \Template 
	 */
	public function set_current_template($index_name)
	{
		unset( $this->current_template );

		if ( is_numeric( $index_name ) )
		{
			$this->current_template = & $this->templates[$index_name];
			return $this;
		}
		else
		{
			foreach ( $this->templates as &$t )
			{
				if ( $t[self::NAME] == $index_name )
				{
					$this->current_template = & $t;
					return $this;
				}
				else
				{
					unset( $t );
				}
			}
			return $this;
		}
	}

	private function get_template_index_by_name($name)
	{
		foreach ( $this->templates as $key => $value )
		{
			if ( $value[self::NAME] == $name )
				return $key;
		}
		return -1;
	}

	/**
	 * Nastavi nazev templatu (pozor u invalidaci)
	 * @param type $name 
	 */
	public function set_name($name)
	{
		$this->current_template[self::NAME] = $name;
		return $this;
	}

	/**
	 *
	 * @param type $name 
	 */
	public function _print($name,$return_as_string = FALSE)
	{
		$index = $this->get_template_index_by_name( $name );

		if ( $index < 0 )
			show_error( "Nepovedlo se nalezt sablonu pomoci nazvu $name" );

		if ($return_as_string)
			return $this->templates[$index][self::VIEW];
		else
			echo $this->templates[$index][self::VIEW];
	}

	/**
	 * Flush slouzi k vypsani souboru, nehlede na cachovani.
	 * Tato view slozka nemusi byt nactena pres load funkci.
	 * @param String $file_name - soubor, ktery se vypise
	 * @param String/Array/boolean/int $arguments - argumenty, ktere se prevedou
	 * na arg0 az argN kde N je pocet dalsich vlozenych
	 * argumentu. Ty se prevedou do view souboru
	 */
	public function flush($file_name, $return_as_string = false, $arguments = null)
	{
		$args = func_get_args();
		$i = 0;
		$data = array();
		foreach ( $args as $arg )
		{
			$i++;
			if ( $i == 1 )
				continue;
			$data['arg' . ($i - 2)] = $arg;
		}
		$this->ci->load->view( "templates/" . $file_name, $data, $return_as_string  );
	}

	/**
	 * Přidá tag pro budouci sablonu kterou chcete
	 * vypsat / invalidovat <br>
	 * Muze se vlozit jak klic a hodnota, tak jen nazev klice.
	 * @param String $key
	 * @param String $value
	 * @return \Template 
	 */
	public function add_tag($key, $value = "")
	{
		// i pro multi-byte (napr. UTF-8)
		$prevodni_tabulka = Array(
			 'ä' => 'a',
			 'Ä' => 'A',
			 'á' => 'a',
			 'Á' => 'A',
			 'à' => 'a',
			 'À' => 'A',
			 'ã' => 'a',
			 'Ã' => 'A',
			 'â' => 'a',
			 'Â' => 'A',
			 'č' => 'c',
			 'Č' => 'C',
			 'ć' => 'c',
			 'Ć' => 'C',
			 'ď' => 'd',
			 'Ď' => 'D',
			 'ě' => 'e',
			 'Ě' => 'E',
			 'é' => 'e',
			 'É' => 'E',
			 'ë' => 'e',
			 'Ë' => 'E',
			 'è' => 'e',
			 'È' => 'E',
			 'ê' => 'e',
			 'Ê' => 'E',
			 'í' => 'i',
			 'Í' => 'I',
			 'ï' => 'i',
			 'Ï' => 'I',
			 'ì' => 'i',
			 'Ì' => 'I',
			 'î' => 'i',
			 'Î' => 'I',
			 'ľ' => 'l',
			 'Ľ' => 'L',
			 'ĺ' => 'l',
			 'Ĺ' => 'L',
			 'ń' => 'n',
			 'Ń' => 'N',
			 'ň' => 'n',
			 'Ň' => 'N',
			 'ñ' => 'n',
			 'Ñ' => 'N',
			 'ó' => 'o',
			 'Ó' => 'O',
			 'ö' => 'o',
			 'Ö' => 'O',
			 'ô' => 'o',
			 'Ô' => 'O',
			 'ò' => 'o',
			 'Ò' => 'O',
			 'õ' => 'o',
			 'Õ' => 'O',
			 'ő' => 'o',
			 'Ő' => 'O',
			 'ř' => 'r',
			 'Ř' => 'R',
			 'ŕ' => 'r',
			 'Ŕ' => 'R',
			 'š' => 's',
			 'Š' => 'S',
			 'ś' => 's',
			 'Ś' => 'S',
			 'ť' => 't',
			 'Ť' => 'T',
			 'ú' => 'u',
			 'Ú' => 'U',
			 'ů' => 'u',
			 'Ů' => 'U',
			 'ü' => 'u',
			 'Ü' => 'U',
			 'ù' => 'u',
			 'Ù' => 'U',
			 'ũ' => 'u',
			 'Ũ' => 'U',
			 'û' => 'u',
			 'Û' => 'U',
			 'ý' => 'y',
			 'Ý' => 'Y',
			 'ž' => 'z',
			 'Ž' => 'Z',
			 'ź' => 'z',
			 'Ź' => 'Z'
		);

		$value = strtr( $value, $prevodni_tabulka );
		$value = str_replace( " ", "", $value );

		$this->current_template[self::TAGS][$key] = $value;
		return $this;
	}

	/**
	 * Vypne moznost ukladat a nacitat z cache.
	 * 
	 * @param type $disable - TRUE (default) vypne cache, FALSE ji zapne
	 * @return \Template 
	 */
	public function disable_caching($disable = TRUE)
	{
		$this->caching_enabled = !$disable;
		return $this;
	}

	/**
	 * Zjisti, jestli je dana sablona zacachovana nebo ne.
	 * Po zavolani tohoto scriptu se tagy zresetujou
	 * @param type $template_name 
	 * @return boolean
	 */
	public function is_template_cached($template_name)
	{
		if ( $this->get_template_index_by_name( $template_name ) < 0 )
		{
			//= jedna se o prvni template	
			$this->set_name( $template_name );
		}
		else
		{
			$this->set_current_template( $template_name );
		}

		$return = $this->cache->get( $this->build_cache_name() );

		$this->current_template[self::TAGS] = null;

		return $return == FALSE ? FALSE : TRUE;
	}

	public function build_cache_name()
	{
		$path = "";

		$path = str_replace( "/", "_", $this->current_template[self::NAME] );

		if ( count( $this->current_template[self::TAGS] ) > 0 )
		{
			foreach ( $this->current_template[self::TAGS] as $key => $value )
			{
				$path .= $value == "" ? '_' . $key : '_' . $key . "[$value]";
			}
		}

		return $path;
	}

}
<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of dmlCache
 *
 * @author Daw
 */
class DMLCache
{
	/**
	 * Tento stitek urcuje prefix pro cache pro dany model.<br>
	 * Kazdy model by mel pred volanim funkce save volat set_name()
	 * ve kterem bude jasne definovat, pro jaky model se tato cache urcuje
	 * @var String 
	 */

	const DML_NAME = 'dml_name';

	/**
	 * Tagy urcujici o jakou cache se jedna
	 * @var String 
	 */
	const TAGS = 'tags';

	/**
	 * Expirace tagu v minutach
	 * @var int
	 */
	const EXPIRATION = 'expiration';

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

	/**
	 * Sberna vsech dat z kterych se pak vygeneruje dana cache
	 * @var Array 
	 */
	private $cache_data = array();

	/**
	 * Constructor pro DMLCache.
	 * Pro pridani tagu pouzijte prikaz addTag()
	 * @param int $expiration - urcuje expiraci dane cache
	 */
	public function __construct($expiration = self::EXP_NO_EXPIRATION)
	{
		$this->cache_data[self::EXPIRATION] = $expiration;
	}

	/**
	 * Nastavo
	 * @param type $dml_name
	 * @return \DMLCache 
	 */
	public function set_name($dml_name)
	{
		$this->cache_data[self::DML_NAME] = $dml_name;
		return $this;
	}

	/**
	 *
	 * @param type $data
	 * @return \DMLCache 
	 */
	public function add_tag($data)
	{
		if ( !is_array( $data ) )
			$data = array($data);

		foreach ( $data as $tag )
		{
			$this->cache_data[self::TAGS][] = $tag;
		}

		return $this;
	}

	public function set_expiration($minutes = self::EXP_DAY)
	{
		$this->cache_data[self::EXPIRATION] = $expiration;
	}

	public function get()
	{
		if ( !isset( $this->cache_data[self::DML_NAME] ) )
			show_error( 'DML Cache: pro cachovani dotazu je potreba nastavit jmeno cache pro dany model.' );

		return $this->cache->get( $this->build_path() );
	}

	public function save($data)
	{
		if ( !isset( $this->cache_data[self::DML_NAME] ) )
			show_error( 'DML Cache: pro cachovani dotazu je potreba nastavit jmeno cache pro dany model.' );

		$this->cache->write( $data, $this->build_path(), $this->cache_data[self::EXPIRATION] );
	}

	/**
	 * Vygeneruje cestu ke cachi pomoci nazvu cache a tagu
	 * @return string 
	 */
	private function build_path()
	{
		$tags = isset( $this->cache_data[self::TAGS] ) && is_array( $this->cache_data[self::TAGS] ) ?
				  implode( '_', $this->cache_data[self::TAGS] ) :
				  null;

		$path = "[" . $this->cache_data[self::DML_NAME] . "]"
				  . "query_"
				  . $tags;

		return $path;
	}

	/**
	 * Invaliduje danou cache. Pri nezadani zadnych tagu se
	 * invaliduji vsechny cache pro danou DML_NAME
	 * @param boolan $cache_group - vymaze vsechny cache, ktery maji dane tagy
	 * @return type 
	 */
	public function invalide($cache_group = FALSE)
	{
		if ( !isset( $this->cache_data[self::DML_NAME] ) )
			show_error( 'DML Cache: pro cachovani dotazu je potreba nastavit jmeno cache pro dany model.' );

		if ( !isset( $this->cache_data[self::TAGS] ) || $cache_group  )
			return $this->cache->delete_group( $this->build_path() );

		return $this->cache->delete( $this->build_path() );
	}

	/**
	 * Priradi cachovaci knihovnu, diky ktere muze tato trida
	 * zacit cachovat
	 * @param Cache $cache
	 * @return \DMLCache 
	 */
	public function assignCache(Cache $cache)
	{
		$this->cache = $cache;
		return $this;
	}

}

?>

<?php

/**
 * DMLTable v sobe obsahuje vsechny informace o 
 * dane tabulce. Tuto tridu vyuziva DML pri 
 * validaci. Tato trida byva cachovana.
 *
 * @name DMLTable
 * @author Pavel Vais
 * @version 1.0
 */
class DMLTable
{
	/**
	 * Indetifikator primary sloupce
	 * @var String 
	 */

	const COL_PRIMARY = "PRI";

	/**
	 * Textovy typ 
	 * @var String 
	 */
	const COL_TYPE_TEXT = 'text';

	/**
	 * Integerovy typ 
	 * @var String 
	 */
	const COL_TYPE_INT = 'int';

	/**
	 * Datovy typ 
	 * @var String 
	 */
	const COL_TYPE_DATE = 'date';

	/**
	 * Datetimovy typ 
	 * @var String 
	 */
	const COL_TYPE_DATETIME = 'datetime';

	/**
	 * Stringovy typ s omezenou  delkou znaku
	 * @var String 
	 */
	const COL_TYPE_VARCHAR = 'varchar';

	/**
	 * Urcuje nazev tabulky - nesmi se menit kvuli
	 * moznosti, ze se cache bude vstahovat k jine tabulce
	 * @var String 
	 */
	private $name;

	/**
	 * Nazev primarniho sloupce
	 * @var String 
	 */
	public $primary_column = "id";

	/**
	 * Soubor vsceh sloupcu
	 * @var Array 
	 */
	private $columns;
	private $chains;
	static $cachedTables;

	public function __construct($table_name)
	{
		DMLBuilder::init();
		$this->change_table_name( $table_name );
		$this->chains = array(
			 'in' => array(),
			 'out' => array()
		);
	}

	/**
	 * Prida specifikaci sloupce
	 * @param String $name
	 * @param String $type
	 * @param int $length
	 * @param boolean $is_nullable
	 * @param boolean $is_primary
	 * @return \DMLTable 
	 */
	public function add_column($name, $type, $length = 0, $is_nullable = TRUE, $is_primary = FALSE)
	{
		$this->columns[$name] = array(
			 'type' => $type,
			 'length' => $length,
			 'is_nullable' => $is_nullable,
			 'is_primary' => $is_primary
		);

		if ( $is_primary )
			$this->primary_column = $name;

		return $this;
	}

	/**
	 * Provaze sloupec s jinou tabulkou.
	 * Outgoing znamena, ze tento sloupec odkazuje na JINOU tabulku
	 * Na jeden sloupec je jen jedna tabulka
	 * @param type $table
	 */
	public function set_outgoing_foreign_data($column, $table)
	{
		$this->chains['out'][$column] = $table;
		return $this;
	}

	/**
	 * Provaze sloupec s jinou tabulkou.
	 * Incoming znamena, ze tento sloupec je pouzivan v JINE tabulce.
	 * Na jeden sloupec muze odkazovat vice tabulek!
	 * (autor je v seznamu knih, ale take v seznamu dluzniku)
	 * @param type $table
	 */
	public function set_incoming_foreign_data($column, $table)
	{
		$this->chains['in'][$column][] = $table;
		return $this;
	}

	/**
	 * Hledam, jestli ma tabulka spojeni na danou jinou tabulku
	 * @param type $table
	 * @return type
	 */
	public function get_incoming_foreign_data()
	{
		return $this->chains['in'];
	}

	public function has_incoming_foreign_data($column)
	{
		return isset( $this->chains['in'][$column] ) ? $this->chains['in'][$column] : false;
	}

	public function has_outgoing_foreign_data($column)
	{
		return isset( $this->chains['out'][$column] ) ? $this->chains['out'][$column] : false;
	}

	public function get_outgoing_foreign_data()
	{
		return $this->chains['out'];
	}

	/**
	 * Je tato tabulka v jine? (IN)
	 * @param String $targetTable
	 * @return boolean
	 */
	public function has_foreign_table_referencing($targetTable)
	{
		foreach ( $this->get_incoming_foreign_data() as $key => $data )
		{
			foreach ( $data as $table )
			{
				if ( $table == $targetTable )
					return $key;
			}
		}
		return FALSE;
	}
	
	/**
	 * Odkazuje tato tabulka na jinou tabulku?
	 * @param String $targetTable - dotaz na jinou tabulku
	 * @return boolean
	 */
	public function has_this_table_referencing($targetTable)
	{
		foreach ( $this->get_outgoing_foreign_data() as $key => $data )
		{
				if ( $data == $targetTable )
					return $key;
		}
		return FALSE;
	}

	public function get_table_name()
	{
		return $this->name;
	}

	/**
	 * Zmeni nazev tabulky
	 * @param String $tableName
	 * @return \DMLTable 
	 */
	public function change_table_name($table_name)
	{
		$this->name = $table_name;
		$this->columns = array();
		return $this;
	}

	/**
	 * Vraci array s informaci o sloupci
	 * nebo false, pokud dany sloupec neexistuje
	 * @param String $name
	 * @return array / boolean 
	 */
	public function get_column($name)
	{
		if ( $this->is_column_exists( $name ) )
			return false;

		return $this->columns[$name];
	}

	/**
	 * Vráti všechny sloupce
	 * @return array 
	 */
	public function get_columns()
	{
		return $this->columns;
	}
	
	public function get_columns_names() {
		$return = array();
		foreach($this->columns as $name => $value)
		{
			$return[] = $name;
		}
		return $return;
	}

	/**
	 * Zepta se, jestli existuje dany sloupec
	 * @param String $name
	 * @return boolean 
	 */
	public function is_column_exists($name)
	{
		return isset( $this->columns[$name] );
	}

	/**
	 * Vrati, jestli tabulka obsahuje informace o sloupcich
	 * @return boolean 
	 */
	public function is_columns_cached()
	{
		return count( $this->columns ) == 0 ? false : true;
	}

	/**
	 * Zacachuje table, takze pri vice volani stejne tridy
	 * se pouzije jeden obraz tabulky (nenacita se stale ze souboru)
	 * @param DMLTable $tableInfo
	 */
	static function cacheTable(DMLTable $tableInfo)
	{
		self::$cachedTables[$tableInfo->get_table_name()] = $tableInfo;
	}

	/**
	 * 
	 * @param type $tableName
	 * @return type
	 */
	static function getCachedTable($tableName)
	{
		return isset( self::$cachedTables[$tableName] ) ? self::$cachedTables[$tableName] : false;
	}

}

class DMLBuilder
{

	/**
	 * Cachovaci system ze tridy Cache
	 * @var Cache
	 */
	protected $cache;
	static $ci;
	static $db;
	static $loaded = false;

	/**
	 * Prefix k souborum vstahujici se k dml
	 * @var String 
	 */

	const CACHE_PREFIX = 'dml_';

	static function init()
	{
		if ( self::$loaded == true )
			return;
			self::$ci = & get_instance();
			self::$ci->load->library( 'cache' );
			self::$db = & self::$ci->db;
			self::$loaded = true;
	}

	/**
	 * Ziska obraz tabulky ze souboru, pri opakovanem pouziti
	 * ziska obraz z cache.
	 * @param type $table_name
	 * @return DMLTable
	 */
	static function loadTableInfo($table_name)
	{
		DMLBuilder::init();
		if ( !is_string( $table_name ) )
			show_error( 'DMLBuilder::loadTableInfo(): $table_name musi byt string!' );

		if ( ($tableInfo = DMLTable::getCachedTable( $table_name )) == false )
			$tableInfo = self::$ci->cache->get( self::CACHE_PREFIX . 'table_' . $table_name );

		if ( $tableInfo != false )
			DMLTable::cacheTable( $tableInfo );

		return $tableInfo !== FALSE ? $tableInfo : DMLBuilder::buildTable( $table_name );
	}

	static function buildTable($table_name)
	{
		//= Aby se provedl dotaz, musi se momentalni ulozit do promenne a smazat.
		//= Pote se zase obnovi. Tim nedojde k naruseni dotazu.
		$stored_session = self::$db->store_session();
		self::$db->_reset_select();



		self::$db->select( 'COLUMN_NAME, DATA_TYPE, IS_NULLABLE,  CHARACTER_MAXIMUM_LENGTH,COLUMN_KEY' )
				  ->where( 'table_name', $table_name )
				  ->where( 'table_schema', self::$db->database )
				  ->group_by( 'COLUMN_NAME' )
				  ->order_by( 'ORDINAL_POSITION' );
		$result = self::$db->get( 'INFORMATION_SCHEMA.COLUMNS' );

		if ( $result->num_rows() == 0 )
			show_error( 'DML: Pri parsovani tabulky vznikla chyba: tabulka ' . $table_name . ' neexistuje.' );


		$tableInfo = new DMLTable( $table_name );

		foreach ( $result->result() as $column )
		{
			$tableInfo->add_column( $column->COLUMN_NAME, $column->DATA_TYPE, $column->CHARACTER_MAXIMUM_LENGTH, $column->IS_NULLABLE == "YES" ? true : false, $column->COLUMN_KEY == DMLTable::COL_PRIMARY ? true : false  );
		}

		//= Nyni ziskam detailni info o provazanosti tabulek
		//= Je nejaky z techto sloupcu obsazen v jinych tabulkach jako foreign klic?
		self::$db->select( 'REFERENCED_COLUMN_NAME,TABLE_NAME' )
				  ->where_in( 'REFERENCED_TABLE_NAME', $table_name );

		$resultDetailed = self::$db->get( 'INFORMATION_SCHEMA.KEY_COLUMN_USAGE' );
		FB::info( $resultDetailed->result() );
		FB::info( self::$db->last_query() );
		if ( $result->num_rows() > 0 )
		{
			foreach ( $resultDetailed->result() as $column )
			{
				$tableInfo->set_incoming_foreign_data( $column->REFERENCED_COLUMN_NAME, $column->TABLE_NAME );
			}
		}
		unset( $resultDetailed );

		//= Nyni zjistime, jestli nejaky sloupec v tabulce neodkazuje na jinou tabulku
		//= Nyni ziskam detailni info o provazanosti tabulek
		self::$db->select( 'COLUMN_NAME, REFERENCED_TABLE_NAME' )
				  ->where_in( 'i.TABLE_NAME', $table_name )
				  ->where( 'i.CONSTRAINT_TYPE', 'FOREIGN KEY' )
				  ->join( 'information_schema.KEY_COLUMN_USAGE k', 'i.CONSTRAINT_NAME = k.CONSTRAINT_NAME ', 'left' );

		$resultDetailed2 = self::$db->get( 'information_schema.TABLE_CONSTRAINTS i' );
		FB::info( $resultDetailed2->result() );
		FB::info( self::$db->last_query() );
		if ( $result->num_rows() > 0 )
		{
			foreach ( $resultDetailed2->result() as $column )
			{
				$tableInfo->set_outgoing_foreign_data( $column->COLUMN_NAME, $column->REFERENCED_TABLE_NAME );
			}
		}



		self::$ci->cache->write( $tableInfo, self::CACHE_PREFIX . 'table_' . $table_name );

		//= Navraceni stareho dotazu, ktery se muze po vytvoreni cache zpracovat
		self::$db->restore_session( $stored_session );

		return $tableInfo;
	}

}
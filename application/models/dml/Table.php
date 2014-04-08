<?php
namespace DML;
/**
 * DMLTable v sobe obsahuje vsechny informace o 
 * dane tabulce. Tuto tridu vyuziva DML pri 
 * validaci. Tato trida byva cachovana.
 *
 * @name DMLTable
 * @author Pavel Vais
 * @version 1.0
 */
class Table
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
		Builder::init();
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
	 * @param string $column - na jaky sloupec je odkazovano z jine tabulky
	 * @param string $table - jaka tabulka odkazuje na tuto tabulku
	 * @param string $fromColumn - udava, jaky sloupec z jine tabulky odkazuje
	 * na tuto tabulku
	 */
	public function set_incoming_foreign_data($column, $table, $fromColumn)
	{
		$this->chains['in'][$column][] = $table . ":" . $fromColumn;
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

	/**
	 * Vrati vsechny Cizi klice, ktere z tabulky odkazuji jinam
	 * @return type
	 */
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
				if ( strpos($table, $targetTable) !== FALSE)
					return $key;
			}
		}
		return FALSE;
	}

	/**
	 * Odkazuje tato tabulka na jinou tabulku?
	 * Muze odkazovat i vicekrat, proto se vzdy vraci array!
	 * (autor_id a owner_id muze odkazovat na stejnou tabulku)
	 * @param String $targetTable - dotaz na jinou tabulku
	 * @return FALSE - tabulka na jinou neodkazuje
	 * ARRAY - seznam sloupecku, ktery na danou tabulku odkazuji
	 */
	public function has_this_table_referencing($targetTable)
	{
		$ret = array();
		$nothing = true;
		foreach ( $this->get_outgoing_foreign_data() as $key => $data )
		{
			if ( $data == $targetTable )
			{
				$nothing = true;
				$ret[] = $key;
			}
		}

		return !$nothing ? false : $ret;
	}

	public function get_table_name()
	{
		return $this->name;
	}

	/**
	 * Donuti DML\Builder aby obnovil cache pro tuto tabulku
	 */
	public function refresh()
	{
		Builder::removeDBCache( $this->get_table_name() );
		return Builder::loadTableInfo( $this->get_table_name() );
	}

	/**
	 * Zmeni nazev tabulky
	 * @param String $tableName
	 * @return Table 
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

	/**
	 * Pouziva se
	 * @return type
	 */
	public function get_columns_names()
	{
		$return = array();
		foreach ( $this->columns as $name => $value )
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
	 * Z IN informace se vrati jak nazev tabulky [0] tak nazev sloupce [1], ktery
	 * na danou tabulku odkazuje
	 * @param string $connection
	 * @return array
	 */
	public function parseInConnection($connection)
	{
		if (($pos = strpos($connection, ':')) === false)
		{
			show_error("Stara table_info u tabulky ".$this->name.'. provedte refresh()!');
		}
		
		return array(
			 substr($connection, 0,$pos),
			 substr($connection, $pos+1)
		);
	}

	/**
	 * Zacachuje table, takze pri vice volani stejne tridy
	 * se pouzije jeden obraz tabulky (nenacita se stale ze souboru)
	 * @param Table $tableInfo
	 */
	static function cacheTable(Table $tableInfo)
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
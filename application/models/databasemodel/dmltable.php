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

	public function __construct($table_name)
	{
		$this->change_table_name($table_name);
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
		if ($this->is_column_exists($name))
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
		return count( $this->columns) == 0 ? false : true;
	}

}

?>

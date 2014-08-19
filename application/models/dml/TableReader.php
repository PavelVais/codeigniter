<?php

namespace DML;

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * 
 * @name TableReader
 * @author	Pavel Vais
 * @version 1.0
 * @copyright Pavel Vais
 */

/**
 * @property CI_Loader $load
 * @property CI_DB_active_record $db
 * @property ConsoleLogger $consolelogger
 */
class TableReader extends Base
{

	private $columns;

	/**
	 * Constructor teto tridy
	 */
	function __construct($table_name)
	{
		parent::__construct( $table_name );

		$this->columns = $this->tableInfo->get_columns();
	}

	/**
	 * Implicitne povoli jen urcita sloupce ke cteni
	 * @param type $array
	 * @return \DML\TableReader
	 */
	function selectColumns($array)
	{
		Helper::to_array( $array );
		$this->columns = $array;
		return $this;
	}

	public function getColumns()
	{
		return $this->columns;
	}

	/**
	 * Zakaze systemu cist hodnoty v danych sloupcich
	 * @param type $array
	 * @return \DML\TableReader
	 */
	function ommitColumns($array)
	{
		Helper::to_array( $array );
		foreach ( $array as $col )
		{
			foreach ( $this->columns as $key => $ccol )
			{
				if ( $col == $ccol )
				{
					unset( $this->columns[$key] );
				}
			}
		}
		return $this;
	}

	function getColumnNames()
	{
		$cols = $this->tableInfo->get_columns();
		$r = array();
		foreach ( $cols as $name => $col )
		{
			if ( $this->canUseCol( strtolower( $name ) ) )
			{
				$r[] = $name;
			}
		}
		return $r;
	}

	public function get()
	{
		$this->db->select( $this->columns );
		return $this->dbGet();
	}

	private function canUseCol($name)
	{
		if ( count( $this->columns ) == 0 )
		{
			foreach ( $this->ommitColumns as $omc )
			{
				if ( $omc == $name )
				{
					return false;
				}
			}
			return true;
		}

		foreach ( $this->columns as $col )
		{
			if ( $col == $name )
			{
				foreach ( $this->ommitColumns as $omc )
				{
					if ( $omc == $name )
					{
						return false;
					}
				}
				return true;
			}
		}
		return false;
	}

}

/* End of file DML.php */
/* Location: ./application/models/DML.php */

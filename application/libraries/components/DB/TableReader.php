<?php

namespace DB;

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * 
 *
 * @category	Libraries
 * @author		Pavel Vais
 * @version		1.0
 */
class TableReader
{

	private $data;

	const HOOK_ROW_OPT = "row_opt";

	/**
	 * @var \Hook\Holder 
	 */
	private $hooks;

	/**
	 * @var \DML\TableReader;
	 */
	public $TRModel;

	/**
	 * @var \HTML\Table; 
	 */
	public $Table;

	function __construct($tableName)
	{
		$this->TRModel = new \DML\TableReader( $tableName );
		$this->Table = new \HTML\Table();
		$this->ar = $this->TRModel->get();
	}

	public function columnSelect($columns)
	{
		$this->TRModel->selectColumns( $columns );
		return $this;
	}

	public function columnOmmit($columns)
	{
		$this->TRModel->ommitColumns( $columns );
		return $this;
	}

	public function generate()
	{
		$this->Table->setHeader( $this->TRModel->getColumns() );

		$result = $this->TRModel->get();
		if ( $result == false )
		{
			return false;
		}
		foreach ( $result as $row )
		{
			$this->Table->addRows($row);
		}
	}

	public function addHook(\Hook\Placer $hook)
	{
		$this->hooks->addHook( $hook );
		return $this;
	}

	private function callHook($place)
	{
		$this->hooks->setPlace( $place );
		$str = '';
		while ( ($next = $this->hooks->getHook()) != null )
		{
			$str .= $next->proceed();
		}
		return $str;
	}

}

/* End of file TableReader.php */
/* Location: ./application/libraries/components/Navigator.php */
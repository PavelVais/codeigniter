<?php

namespace HTML;

/**
 * 
 * use case:
 * Table->			
 */
class Table
{

	private $th;
	private $rows;
	public $tableClass = 'table';
	public $tableID;
	public $order = array();

	const ROW_CLASS_IDENTIFIER = 'table_row_class';

	public function __construct()
	{
		$this->rows = array();
	}

	public function setHeader($cols)
	{
		if ( !is_array( $cols ) )
		{
			$cols = explode( ',', $cols );
		}

		if ( is_object( $cols ) )
		{
			$cols = (array) $cols;
		}

		$this->th = $cols;
	}

	public function addRows($rows)
	{
		$this->rows = $rows;
		return $this;
	}

	public function orderBy($columns)
	{
		\DML\Helper::to_array( $columns );
		$this->order = $columns;
		return $this;
	}

	public function generate()
	{
		$table = Element::open( "table" )
				  ->addAttribute( 'class', $this->tableClass );

		if ( $this->tableID != null )
		{
			$table->addAttribute( 'id', $this->tableID );
		}
		$this->injectHeader( $table );
		$this->injectRows( $table );
		return $table->generate();
	}

	public function injectHeader(\HTML\Element &$table)
	{
		$el_row = Element::open( "tr" );
		foreach ( $this->th as $col )
		{
			$el_row->append( Element::open( 'th' )->appendString( $col ) );
		}
		$table->append( $el_row );
	}

	public function injectRows(\HTML\Element &$table)
	{
		foreach ( $this->rows as &$row )
		{
			$el_row = Element::open( "tr" );
			$row = (array) $row;
			if ( isset( $row[self::ROW_CLASS_IDENTIFIER] ) )
			{
				$el_row->addAttribute( 'class', $row[self::ROW_CLASS_IDENTIFIER] );
				unset( $row[self::ROW_CLASS_IDENTIFIER] );
			}

			if ( isset( $row['hook'] ) && $row['hook'] instanceof \Hook\Placer )
			{
				$hook = $row['hook'];
				unset( $row['hook'] );
				$hook->setArgs( array(&$el_row, &$row) )->proceed();
			}

			foreach ( $row as $value )
			{
				if ( is_array( $value ) )
				{
					$td = Element::open( "td" );
					$this->injectTDHook( $td, $value );
					$el_row->append( $td, true );
				}
				else
				{
					$el_row->append( Element::open( "td" )
										 ->appendString( $value )
					);
				}
			}

			$table->append( $el_row, true );
		}
		return $this;
	}

	private function injectTDHook(Element &$row, $value)
	{
		if ( isset( $value['hook'] ) )
		{
			if ( $value['hook'] instanceof \Hook\Placer )
			{
				$hook = $value['hook'];
				unset( $value['hook'] );
				$hook->setArgs( array(&$row, $value) )->proceed();
			}
		}
	}

}

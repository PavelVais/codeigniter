<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * DBConditionEntry je model komunikujici chytre s databazi.
 * Jako Abstraktni tridu je mozne DM pouzivat jako rodice pro dalsi tridy.
 * 
 * @name DBEntryRestricion
 * @author	Pavel Vais
 * @version 1.0
 * @copyright Pavel Vais
 * 
 * 
 * 
 * IF_INSERT_IF_NULL_NEW_DATE => pokud se jedna o insert, 
 */
class DBEntryRestricion
{

	static $COL_LENGTH = 1;
	static $IS_BOOLEAN = 2;
	static $IS_NOT_NULL = 4;
	static $IS_EMAIL = 8;
	static $IS_INTEGER = 16;
	private $column_max_length;
	private $column_type;

	public function __construct($column_max_length, $column_type = null)
	{

		$this->column_max_length = $column_max_length;
		$this->column_type = $this->transformArrayToBitmap( ($this->transformToArray( $column_type ) ) );
		return $this;
	}

	public function addRestriction($restriction, $argument = null)
	{
		if ( $restriction == self::$COL_LENGTH )
			$this->column_max_length = $argument;

		else
		if ( !$this->isInBitmask( $restriction, $where ) )
		{
			$this->column_type += $restriction;
		}
		return $this;
	}

	public function hasRestrictions($type = -1)
	{
		if ( !is_numeric( $type ) )
			show_error( 'DBEntryRestricion: funkce getRestrictions: argument $type musí být číslo!' );
		if ( $type == -1 )
			return array(
				 'col_length' => $this->column_max_length,
				 'is_boolean' => $this->isInBitmask( self::$IS_BOOLEAN, $this->column_type ) ? TRUE : FALSE,
				 'is_not_null' => $this->isInBitmask( self::$IS_NOT_NULL, $this->column_type ) ? TRUE : FALSE,
				 'is_email' => $this->isInBitmask( self::$IS_EMAIL, $this->column_type ) ? TRUE : FALSE,
				 'is_integer' => $this->isInBitmask( self::$IS_INTEGER, $this->column_type ) ? TRUE : FALSE,
			);
		else
		{
			switch ($type)
			{
				case self::$IS_BOOLEAN:
					return $this->isInBitmask( self::$IS_BOOLEAN, $this->column_type ) ? TRUE : FALSE;
					break;
				case self::$IS_NOT_NULL:
					return $this->isInBitmask( self::$IS_NOT_NULL, $this->column_type ) ? TRUE : FALSE;
					break;
				case self::$COL_LENGTH:
				default:
					return $this->column_max_length;
					break;
				case self::$IS_EMAIL:
					return $this->isInBitmask( self::$IS_EMAIL, $this->column_type ) ? TRUE : FALSE;
					break;
				case self::$IS_INTEGER:
					return $this->isInBitmask( self::$IS_INTEGER, $this->column_type ) ? TRUE : FALSE;
					break;
			}
		}
	}

	private function transformToArray($value)
	{
		if ( is_null( $value ) )
			return null;

		if ( !is_array( $value ) )
		{
			return array($value);
		}
		return $value;
	}

	private function transformArrayToBitmap($array)
	{
		if ( !is_array( $array ) )
			return;

		$bitmap = 0;

		foreach ( $array as $value )
		{
			if ( !is_numeric( $value ) )
				show_error( "DBConditionEntry: Hodnota '$value' se nedá převést na bitmapové číslo!" );
			$bitmap += $value;
		}

		return $bitmap;
	}

	private function isInBitmask($what, $where)
	{
		return ($where & $what) == $what ? TRUE : FALSE;
	}

}

/* End of file DBEntryRestricion.php */
/* Location: ./application/models/DatabaseModel/DBEntryRestricion.php */

	
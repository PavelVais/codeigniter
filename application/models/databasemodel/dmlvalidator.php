<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * DMLValidator umoznuje validovat vlozena data dle informaci
 * nactenych z tabulky
 * 
 * @name DMLValidator
 * @author	Pavel Vais
 * @copyright Pavel Vais
 * @version 1.0
 */
abstract class DMLValidator
{
	
	/**
	 * Constructor teto tridy
	 */
	function __construct(){}

	/**
	 * Checkuje data oproti ruznym nastaveni sloupcu
	 * @param type $data_type
	 * @param type $data_value
	 * @return boolean 
	 */
	public function check_data_type($data_type, $data_value)
	{
		switch ($data_type)
		{
			case DMLTable::COL_TYPE_INT:
				return is_numeric( $data_value );
			case DMLTable::COL_TYPE_DATE:
			case DMLTable::COL_TYPE_DATETIME:
				return preg_match( "/^[0-9_\-: ]+$/", $data_value );
			default:
				return TRUE;
		}
	}

}

/* End of file DatabaseModel.php */
/* Location: ./application/models/DatabaseModel.php */

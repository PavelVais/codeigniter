<?php

namespace DB;

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * ModelSorter Class
 *
 * Partial Caching library for CodeIgniter
 *
 * @category	Libraries
 * @author		Pavel Vais
 * @version		1.0
 */
class ModelSorter
{

	private $tableName;

	function __construct($tableName)
	{
		$this->tableName = $tableName;
	}
	
	/**
	 * 
	 * @param array $data - array IDcek, kde jejich poradi urcuje i poradi v DB
	 * @param string $sortColumn
	 * @param string $primaryColumn
	 */
	function sort($data,$sortColumn,$primaryColumn)
	{
		$ci = & get_instance();
		$l = count($data);
		for ( $i = 0; $i < $l; $i++ )
		{
			$ci->db->query( "UPDATE `$this->tableName` SET `$sortColumn`=" . $i . " WHERE `$primaryColumn` ='" . mysql_real_escape_string( $data[$i] ) . "'" );
		}
	}
	

	

}

/* End of file ModelSorter.php */
/* Location: ./application/libraries/components/ModelSorter.php */
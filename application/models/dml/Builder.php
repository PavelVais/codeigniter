<?php
namespace DML;
class Builder
{

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
	 * @param type $tableName
	 * @return Table
	 */
	static function loadTableInfo($tableName)
	{
		self::init();
		if ( !is_string( $tableName ) )
			show_error( 'DML\Builder::loadTableInfo(): $table_name musi byt string!' );

		if ( ($tableInfo = Table::getCachedTable( $tableName )) == false )
			$tableInfo = self::$ci->cache->get( self::CACHE_PREFIX . 'table_' . $tableName );

		if ( $tableInfo != false )
			Table::cacheTable( $tableInfo );

		return $tableInfo !== FALSE ? $tableInfo : Builder::buildTable( $tableName );
	}

	static function buildTable($table_name)
	{
		//= Aby se provedl dotaz, musi se momentalni ulozit do promenne a smazat.
		//= Pote se zase obnovi. Tim nedojde k naruseni dotazu.
		$stored_session = self::$db->store_session();
		self::$db->_reset_select();

		\FB::info( 'BUILDTABLE procedure() table: ' . $table_name );

		self::$db->select( 'COLUMN_NAME, DATA_TYPE, IS_NULLABLE,  CHARACTER_MAXIMUM_LENGTH,COLUMN_KEY' )
				  ->where( 'table_name', $table_name )
				  ->where( 'table_schema', self::$db->database )
				  ->group_by( 'COLUMN_NAME' )
				  ->order_by( 'ORDINAL_POSITION' );
		$result = self::$db->get( 'INFORMATION_SCHEMA.COLUMNS' );

		if ( $result->num_rows() == 0 )
			show_error( 'DML: Pri parsovani tabulky vznikla chyba: tabulka ' . $table_name . ' neexistuje.' );


		$tableInfo = new Table( $table_name );

		foreach ( $result->result() as $column )
		{
			$tableInfo->add_column( $column->COLUMN_NAME, $column->DATA_TYPE, $column->CHARACTER_MAXIMUM_LENGTH, $column->IS_NULLABLE == "YES" ? true : false, $column->COLUMN_KEY == Table::COL_PRIMARY ? true : false  );
		}

		//= Nyni ziskam detailni info o provazanosti tabulek
		//= Je nejaky z techto sloupcu obsazen v jinych tabulkach jako foreign klic?
		self::$db->select( 'REFERENCED_COLUMN_NAME,TABLE_NAME,COLUMN_NAME' )
				  ->where_in( 'REFERENCED_TABLE_NAME', $table_name );

		$resultDetailed = self::$db->get( 'INFORMATION_SCHEMA.KEY_COLUMN_USAGE' );
		\FB::info( $resultDetailed->result() );
		\FB::info( self::$db->last_query() );
		if ( $result->num_rows() > 0 )
		{
			foreach ( $resultDetailed->result() as $column )
			{
				$tableInfo->set_incoming_foreign_data( $column->REFERENCED_COLUMN_NAME, $column->TABLE_NAME, $column->COLUMN_NAME );
			}
		}
		unset( $resultDetailed );

		//= Nyni zjistime, jestli nejaky sloupec v tabulce neodkazuje na jinou tabulku
		//= Nyni ziskam detailni info o provazanosti tabulek
		self::$db->select( 'COLUMN_NAME, REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME' )
				  ->where_in( 'i.TABLE_NAME', $table_name )
				  ->where( 'i.CONSTRAINT_TYPE', 'FOREIGN KEY' )
				  ->join( 'information_schema.KEY_COLUMN_USAGE k', 'i.CONSTRAINT_NAME = k.CONSTRAINT_NAME ', 'left' );

		$resultDetailed2 = self::$db->get( 'information_schema.TABLE_CONSTRAINTS i' );
		\FB::info( $resultDetailed2->result() );
		\FB::info( self::$db->last_query() );
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

	/**
	 * Odstrani cache. Pokud neni vyplny nazev tabulky, odstrani se vsechny 
	 * dml_ cache.
	 * @param string $tableName - nazev tabulky pro vymazani
	 * @return boolean
	 */
	static function removeDBCache($tableName = null)
	{
		self::init();
		if ( $tableName == null )
		{
			self::$ci->cache->delete_group( self::CACHE_PREFIX );
			return true;
		}

		self::$ci->cache->delete( self::CACHE_PREFIX . 'table_' . $tableName );
		return true;
	}

	/**
	 * Vsem selektum to prida prefix a vrati array
	 * @param Array/String $select
	 * @param String $tablePrefix
	 * @return Array
	 */
	static function prepareSelect($select, $tablePrefix, $AS_prefix = null)
	{

		if ( $select == null || $select == '*' )
			return array($tablePrefix . '.*');

		if ( !is_array( $select ) )
			$select = explode( ',', $select );
		foreach ( $select as &$s )
		{
			$s = (strpos( $s, '.' ) === FALSE ? $tablePrefix . '.' . trim( $s, ' ' ) : $s) . ($AS_prefix != null ? ' AS ' . $AS_prefix . $s : '');
		}

		return $select;
	}

}

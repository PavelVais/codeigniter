<?php
/**
		 * Jak tuto tabulku chci vyuzivat:
		 * Tabulka MN nemusi byt propojovaci!!!
		 * 
		 * 1. pripad:
		 * 		books <- tags_books <- tags
		 * $this->dbJoinMN('tags'[,'tags_books'])
		 * proces:
		 * 		1) books projede vsechny svoje IN connection
		 * 		2) napoji se na vsechny tabulky z IN connection
		 * 		3) koukne, jestli nejaka z nich odkazuje na tags
		 * 		4a) Jestli jich je vic, musi se udat druhy argument, ktery upresnuje
		 * 			 nazev IN tabulky
		 * 		5) v IN tabulce se zavola new Join na OUT tabulku ;)
		 * 2: pripad:
		 * 		books <- titles (id, book_id, title, language_id)
		 * $this->dbJoinMN('titles')
		 * proces:
		 * 		1) books projede vsechny svoje IN connection
		 * 		2) jmenuje se tabulka primo tak, jak je urcen prvni parametr?
		 * 		3)	ano? tak rovnou dame join a nazdar bazar		
		 */
/**
 * DMLJoin je jednoduchá třída
 *
 * @name DMLJoin
 * @author Pavel Vais
 * @version 1.0
 */
class DMLJoinMN extends \DMLJoin
{
	/**
	 * @var DMLTable
	 */
	private $tableInfo;
	private $joinMethod;

	public function __construct($table, DMLTable $tableInfo, $referencingColumn = null)
	{
		$this->tableName = $tableInfo->get_table_name();
		$this->tableInfo = $tableInfo;
		$this->targetTableName = $table;
		$this->joinMethod = '';
		$this->select();
	}

	/**
	 * 
	 * @param CI_DB_active_record $db
	 * @param type $tablePrefix
	 */
	public function execute(CI_DB_active_record $db, &$DBresult)
	{
		$tableName = $this->tableName;

		$result = $this->findINConnection( $this->targetTableName );
		$targetTableInfo = DMLBuilder::loadTableInfo( $result['extraJoinTable'] == null ? $result['fromTable'] : $result['extraJoinTable']  );


		if ( $result == false )
		{
			show_error( "DMLJoinMN(): Neni zadny odkaz z tabulky " . $this->tableName . " na tabulku " . $this->targetTableName );
		}

		if ( !isset( $DBresult[0]->{$result['toColumn']} ) )
		{
			show_error( 'DMLJoinMN(): v Resultu neni sloupec ' . $result['toColumn'] );
		}

		$whereIn = $this->getWhereINData( $DBresult, $result['toColumn'] );

		if ( empty( $whereIn ) )
		{
			// Neni treba cokoli dal resit, byl nam dan prazdny vysledek
			return;
		}

		//= Nyni poresime select
		if ( $this->select == '*' && $result['extraJoinTable'] == null )
		{
			$columns = $targetTableInfo->get_columns_names();
			if ( $result['extraJoinTable'] != null )
			{
				unset( $columns[$result['extraJoinColumn']] ); //= Nemusime znova stahovat IDcko, ktere uz je v cizim klici
			}
		}
		else
		{
			$columns = $this->select;
		}

		$columns = DMLBuilder::prepareSelect( $columns, $result['fromTable'] );
		//= Prida se neescapovany selekty
		if ( count( $this->selectNoEscape ) > 0 )
		{
			$db->select( $this->selectNoEscape, false );
		}

		if ( $result['extraJoinTable'] != null )
		{
			$join = new DMLJoin( $result['extraJoinTable'], $this->tableInfo, $result['extraJoinColumn'] );

			if ( $this->joinMethod !== '' )
				$join->{$this->joinMethod}();

			$join->execute( $db );
		}

		$db->select( $columns )
				  ->where_in( $result['joinFromColumn'], $whereIn );

		$DBresultNEW = $db->get( $result['fromTable'] );

		$this->pairIt( $DBresult, $DBresultNEW, $targetTableInfo->get_table_name(), $result['toColumn'], $result['joinFromColumn'] );
	}

	/**
	 * Sparuje to vysledky
	 * @param type $orig_result - puvodni result
	 * @param type $result - nynejsi result
	 * @param type $newColumnName - jak se bude jmenovat novy sloupec s novym resultem
	 * @param type $groupByOldColumn - podle ktereho klice ze stareho resultu se budou vysledky seskupovat
	 * @param type $groupByNewColumn - podle ktereho klice z noveho resultu se budou vysledky seskupovat
	 */
	private function pairIt(&$orig_result, $result, $newColumnName, $groupByOldColumn, $groupByNewColumn)
	{
		$result = $result->result();
		foreach ( $orig_result as &$row )
		{
			foreach ( $result as $k => &$r )
			{
				if ( $row->$groupByOldColumn == $r->$groupByNewColumn )
				{
					unset( $r->$groupByNewColumn );
					$row->{$newColumnName}[] = $r;
					unset( $result[$k] );
				}
			}

			if ( !isset( $row->{$newColumnName} ) )
				$row->{$newColumnName} = false;
		}
	}

	/**
	 * Projede vsechny IN connectiony a rovnou patricny propojeni propoji
	 * @param string $actTable - jakou tabulku hledame
	 * @return null
	 */
	private function findINConnection($table)
	{
		/**
		 * Hledam books_tags nebo title
		 * u vsech IN connection projedu jejich OUT connection
		 */
		$return = null;
		foreach ( $this->tableInfo->get_incoming_foreign_data() as $columnName => $index )
		{
			foreach ( $index as $actTable )
			{
				list($inTable, $inColumn) = $this->tableInfo->parseInConnection( $actTable );
				if ( $inTable == $table )
				{
					// MN odkazuje POUZE na "spojovaci" tabulku, nebude se provadet zadny join, prace zde skoncila
					$this->tableInfo = DMLBuilder::loadTableInfo( $inTable );
					return array(
						 'fromTable' => $inTable, //tabulka odkazujici na books
						 'joinFromColumn' => $inColumn, //column odkazujici na books
						 'toTable' => $table,
						 'toColumn' => $columnName,
						 'extraJoinTable' => null,
						 'extraJoinColumn' => null
					);
				}

				/**
				 * Neresi problem, kdyz z tabulky books vedou dva IN zmrdi a oba odkazuji
				 * na nasi hledanou tabulku!
				 * @todo 2x IN problem nereseni
				 */
				if ( ($result = $this->findCorrespondingOUTTable( $table, $inTable )) != FALSE )
				{
					$this->tableInfo = DMLBuilder::loadTableInfo( $inTable );
					return array(
						 'fromTable' => $inTable,
						 'joinFromColumn' => $inColumn,
						 'toTable' => $actTable,
						 'toColumn' => $columnName,
						 'extraJoinTable' => $result[0],
						 'extraJoinColumn' => $result[1]
					);
				}
			}
		}
		return false;
	}

	/**
	 * Vrati FALSE, pokud dana tabulka nikam neodkazuje,
	 * jinak vrati string (s tabuklou jednou!!) s vsemi tabulkami, ktere nekam odkazuji
	 * @todo MUSIME VYRESIT!!! 
	 *  - co kdyz MN tabulka odkazuje dvakrat na stejnou tabulku?
	 * 		(tzn return array ma 2 prvky misto jednoho)
	 * @param type $searchedTable
	 * @param type $currentTable
	 * @return string/false
	 */
	private function findCorrespondingOUTTable($searchedTable, $currentTable)
	{
		$tableInfo = DMLBuilder::loadTableInfo( $currentTable );

		foreach ( $tableInfo->get_outgoing_foreign_data() as $column => $table )
		{
			if ( $table == $searchedTable )
			{
				return array($table, $column);
			}
		}
		return false;
	}

	/**
	 * Funkce, ktera veme z resultu vsechny IDcka, podle kterych se pak nasimuluje
	 * sql dotaz (idcka se vlozi do where_in funkce)
	 * @param array $result - result, ze ktereho se budou cisla brat
	 * @param string $columnName - nazev tabulky
	 * @return array
	 */
	public function getWhereINData($result, $columnName)
	{
		$dataWhere = array();
		foreach ( $result as $rows )
		{
			$dataWhere[] = $rows->$columnName;
		}
		return $dataWhere;
	}

}

<?php

/**
 * DMLJoin je jednoduchá třída
 *
 * @name DMLJoin
 * @author Pavel Vais
 * @version 1.0
 */
class DMLJoin
{
	/*
	 * Pokud je jeden 
	 * $this->join('adresees')
	 */

	public $tableName;
	public $targetTableName;
	public $referencingColumn;
	private $callback;
	private $select;
	private $selectNoEscape;
	private $generatedInfo;

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

		/**
		 * Zjistim, jakej sloupecek vlastne musim propojit
		 * Zadnej? error
		 */
		if ( ($referencingColumns = $this->tableInfo->has_this_table_referencing( $this->targetTableName )) == FALSE )
		{
			show_error( 'Join(): Tabulka ' . $this->tableName . ' neodkazuje na tabulku ' . $table );
		}

		// Naslo se vice sloupecku, ktere odkazuji z jedne tabulky na jinou a system nevi ktery pouzit!
		if ( count( $referencingColumns ) > 1 && $referencingColumn == null )
		{
			show_error( 'Join(): Tabulka ' . $this->tableName . ' ma vice odkazujicich sloupcu (' . implode( ', ', $referencingColumns ) . ') na tabulku ' . $table . '. V prikazu dbJoin($table, $fromColumn) urcete druhy argument.' );
		}

		if ( count( $referencingColumns ) == 1 && $referencingColumn == null )
		{
			$this->referencingColumn = $referencingColumns[0];
		}
		else
		{
			$this->referencingColumn = $referencingColumn;
		}
		FB::info( $this->referencingColumn, 'ref' );
	}

	public function getGeneratedInfo($type = null)
	{
		return $type == null ? $this->generatedInfo : $this->generatedInfo[$type];
	}

	public function setGeneratedInfo($type, $generatedInfo)
	{
		$this->generatedInfo[$type] = $generatedInfo;
		return $this;
	}

	/**
	 * Jedna se o sloupecky, ktere se maji pridruzit k dotazu.
	 * Pokud se u sloupecku uvede "!", sloupecek se nebude escapovat
	 * @param mixed(Array/String) $columns - pokud neni uveden, joinou se
	 * vsechny sloupce
	 */
	public function select($columns = '*')
	{
		if ( $columns == '*' )
		{
			$this->select = '*';
			return;
		}

		if ( !is_array( $columns ) )
		{
			$columns = explode( ',', $columns );
		}

		$this->filterSelects( $columns );
		return $this;
	}

	/**
	 * Roztridi selekty na noEscape selektory a na normalni selekty
	 * @param array $columns
	 * @return \DMLJoin
	 */
	private function filterSelects($columns)
	{
		$a = array();
		foreach ( $columns as $col )
		{
			if ( strpos( $col, '!' ) !== false )
				$this->selectNoEscape[] = substr( $col, 1 );
			else
			{
				$a[] = $col;
			}
		}
		if ( !empty( $a ) )
		{
			$this->select = $a;
		}
		return $this;
	}

	/**
	 * Pred vykonanim samotneho dotazu se zavola callback, ktery muze 
	 * neco upresnit jeste
	 * @param type $anonymousFunction
	 */
	public function calllback($anonymousFunction)
	{
		$this->callback = $anonymousFunction;
	}

	/**
	 * 
	 * @param CI_DB_active_record $db
	 * @param type $tablePrefix
	 */
	public function execute(CI_DB_active_record $db, $tablePrefix = '', $refactoredTableName = null, $extraSelectPrefix = '')
	{
		/**
		 * FLOW
		 * 1. Odkazuje tahle tabulka na jinou?
		 * 2. [ano] Je odkaz unikatni? (neni vice sloupecku, ktere na jinou tabulku odkazuji)
		 * 		- Napriklad knizka muze mit odkaz na autora ale take na vlastnika
		 * 		  v tu chvili musi byt urceno, i pres jaky sloupecek se provadi join
		 * 3. Zjistit, ktery sloupecek odkazuje na jinou tabulku
		 * 4. Zjistit, na jaky sloupecek v jine tabulce odkazuje
		 * 5. Pridat afixy
		 * 6. sestrojit join
		 */
		$tableName = $this->tableName; // Odkazujici tabulka
		$referencingColumn = $this->referencingColumn; // Odkazujici sloupecek
		$targetTable = $this->targetTableName . $tablePrefix;


		/**
		 * Nyni musime ziskat nazev sloupecku, na ktery tabulka odkazuje
		 */
		$targetTableInfo = DMLBuilder::loadTableInfo( $this->targetTableName );
		$targetColumn = $targetTableInfo->has_foreign_table_referencing( $tableName );

		if ( $targetColumn == FALSE )
		{
			FB::info( $targetTableInfo, '' );
			show_error( 'DBJoin() Tabulka ' . $targetTable . ' neni obsazena v tabulce ' . $tableName );
		}

		if ( $refactoredTableName != null )
			$tableName = $refactoredTableName;


		//= Nyni poresime select
		if ( $this->select == '*' )
		{
			$columns = $targetTableInfo->get_columns_names();
			unset( $columns[$targetColumn] ); //= Nemusime znova stahovat IDcko, ktere uz je v cizim klici
		}
		else
		{
			$columns = $this->select;
		}
		$AS_prefix = $this->discoverNaming( $extraSelectPrefix . $referencingColumn );


		$columns = DMLBuilder::prepareSelect( $columns, $targetTable, $AS_prefix );
		// Zavola callback, jeli k dispozici
		if ( $this->callback != null && is_callable( $this->callback ) )
		{
			call_user_func( $this->callback );
		}

		//= Prida se neescapovany selekty
		if ( count( $this->selectNoEscape ) > 0 )
		{
			$db->select( $this->selectNoEscape, false );
		}

		$this->setGeneratedInfo( 'AS', $AS_prefix )
				  ->setGeneratedInfo( 'tableName', $targetTable )
				  ->setGeneratedInfo( 'targetColumn', $targetColumn );


		//= Vlozi se join!!
		$db->select( $columns )
				  ->join( $this->targetTableName . ($tablePrefix == '' ? '' : ' AS ' . $targetTable), $targetTable . '.' . $targetColumn . ' = ' . $tableName . '.' . $referencingColumn, $this->joinMethod );
	}

	/**
	 * MN tabuka se vlozi jako Left join
	 */
	public function left()
	{
		$this->joinMethod = 'left';
	}

	/**
	 * MN tabulka se vlozi jako Right join
	 */
	public function right()
	{
		$this->joinMethod = 'right';
	}

	/**
	 * Z nazvu sloupecku se snazi vytvorit vhodny prefix, diky kteremu nedojde ke kolizi nazvu.
	 * Kdyz se pouzije vice joinu na stejnou tabulku, je potreba aby se vscehno spravne rozfiltrovalo.
	 * pokud sloupecek obsahuje '_id' tak to predtim se veme jako prefix pro selekty
	 * z joinu.
	 * takze owner_id -> owner_name, owner_email .... 
	 * Pokud tato konvenze neni zachovana a sloupecek se nejmenuje xxx_id,
	 * pouzije se cely nazev sloupecku (s jiz aplikovanym prefixem)
	 * takze owner -> owner_name,owner_email 
	 * @param string $referencingColumn - nazev sloupecku
	 * @return string
	 */
	private function discoverNaming($referencingColumn)
	{
		if ( ($pos = strpos( $referencingColumn, '_id' )) !== false )
		{
			return substr( $referencingColumn, 0, $pos ) . '_';
		}

		return $referencingColumn . '_';
	}

}

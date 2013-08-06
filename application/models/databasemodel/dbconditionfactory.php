<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * DatabaseConditionFactory je soubor metod obsluhujici DBConditionEntry.
 * Umi chytre validovat jednotlive tridy pomoci stavu, ktere jsou do teto tridy
 * zasilany. V pripade erroru je sbira do kontejneru, ktery pote nadrizena trida
 * precte.
 * 
 * @name DBConditionFactory
 * @author	Pavel Vais
 * @version 1.0
 * @copyright Pavel Vais
 */
class DBConditionFactory
{
	/**
	 * pole trid DBConditionEntry
	 * @var array 
	 */
	private $columns_spec = array();
	
	private $current_state;
	
	private $errors = array();

	public function __construct()
	{
		
	}

	/**
	 * Zvaliduje data, ktera podlehaji danemu stavu (DBConditionEntry::$STATE_XXX)
	 * @param int $state
	 * @return DBConditionFactory
	 */
	public function validate($state)
	{
				  
		foreach ( $this->columns_spec AS $column )
		{
			$column->setCurrentState($state);
			/* @var $column DBConditionEntry */
			if ( $column->isReady( $state ) )
			{
				$column->performAction();
			}
		}
		return $this;
	}

	/**
	 * Vrati validovane data
	 * @return array
	 */
	public function retrieveValidatedData()
	{
		$data = array();
		/* @var $column DBConditionEntry */
		foreach ( $this->columns_spec AS $column )
		{
			try
			{
				if ( !is_null( $column->getValue() ) )
					$data[(string) $column] = $column->getValue();
			}
			catch (DBConditionExceptions $exc)
			{
				if ( !$this->hasColumnError( (string) $column ) )
					$this->addError( $exc->getErrorMessage(), (string) $column );
			}
		}
		return $data;
		
	}

	/**
	 * Nacte array data ([nazev_sloupce] => 'hodnota') a 
	 * paklize k nim najde prislusny DBConditionEntry, zapise do nich danou hodnotu.
	 * V pripade nenalezeni prislusne tridy se dana trida s primo danou hodnotou
	 * vytvori.
	 * @param array $columnValues 
	 */
	public function loadColumnValuesData($columnValues)
	{
		foreach ( $columnValues AS $value => $key )
		{
			$founded = FALSE;

			foreach ( $this->columns_spec AS $column )
			{
				/* @var $column DBConditionEntry */
				if ( $column->__toString() == $value )
				{
					try
					{
						$column->setValue( $key );
					}
					catch (DBConditionExceptions $exc)
					{
						$this->addError( $exc->getErrorMessage(), (string) $column );
					}

					$founded = TRUE;
					break;
				}
			}

			if ( !$founded )
			{
				$new_entry = new DBConditionEntry( $value, null, null );
				$new_entry->setValue( $key );
				$this->addNewColumnSpec( $new_entry );
			}
		}
	}

	/**
	 * Nacte pole trid DBConditionEntry.
	 * @param DBConditionEntry Array $dataArray 
	 */
	public function loadColumnData($dataArray)
	{
		/* @var $data DBConditionEntry */
		foreach ( $dataArray AS $data )
			if ( $data instanceof DBConditionEntry )
			{
				$this->addNewColumnSpec( $data );
			}
			else
			{
				show_error( "DBConditionFactory: loadData funkce neobsahuje array DBConditionEntry tříd!" );
			}
	}

	/**
	 * Prida do teto tridy dalsi specifikace ke konkretnimu databazovemu sloupci.
	 * @param DBConditionEntry $DBEntry
	 * @return DBConditionFactory 
	 */
	public function addNewColumnSpec(DBConditionEntry $DBEntry)
	{
		$this->columns_spec[] = $DBEntry;
		return $this;
	}

	private function getSpecificsColumnEntry($EntryTriggerCondition)
	{
		
	}

	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 *
	 * @param String $error 
	 */
	private function addError($error, $column)
	{
		$this->errors[] = array($error, $column);
	}

	private function hasColumnError($columnName)
	{
		foreach ( $this->errors AS $error )
		{

			if ( $error[1] == $columnName )
				return TRUE;
		}

		return FALSE;
	}

}

/* End of file DBConditionFactory.php */
/* Location: ./application/models/DatabaseModel/DBConditionFactory.php */

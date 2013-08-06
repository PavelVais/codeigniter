<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * DBConditionEntry....
 * 
 * @name DBConditionEntry
 * @author	Pavel Vais
 * @version 1.0
 * @copyright Pavel Vais
 * 
 */
class DBConditionEntry
{

	static $STATE_UPDATE = 1;
	static $STATE_INSERT = 2;
	static $STATE_SAVE = 3;
	static $STATE_UPDATE_IGNORE_ALL = 4;

	/*
	 * TRIGGERY se urcuji pomoci bitmasky
	 */
	static $IF_UPDATE = 1;
	static $IF_INSERT = 2;
	static $IF_NULL = 4;
	static $IF_NOT_NULL = 8;
	static $IF_BOOLEAN = 16;
	static $IF_NOT_BOOLEAN = 32;

	/*
	 * AKCE muze byt jen jedna!!
	 */
	static $NEW_DATE = 1;
	static $NEW_DATETIME = 2;
	static $NEW_VALUE = 4;

	/* static $IF_NOT_NULL = 8;
	  static $IF_BOOLEAN = 16;
	  static $IF_NOT_BOOLEAN = 32; */
	private $triggers;
	private $actions;
	private $columnName;

	/**
	 * Restrikce stahovane na danou tridu
	 * @var DBEntryRestricion 
	 */
	private $restriction;
	private $current_state;
	private $value;
	private $is_locked = FALSE;

	/**
	 * KDY se podminka spusti a CO udela
	 * KDY: IF_UPDATE, IF_INSERT, IF_NULL, IF_BOOLEAN
	 * CO: NEW_DATE , NEW_DATETIME , VALUE
	 * @param type $name 
	 */
	public function __construct($columnName, $triggers, $action, DBEntryRestricion $restriction = null)
	{
		if ( $triggers == null && $action != null )
			show_error( "DBConditionEntry: Akce nemůže bez spínače (triggeru) existovat!" );
		$this->actions = $action;
		$this->triggers = $this->transformArrayToBitmap( ($this->transformToArray( $triggers ) ) );
		$this->columnName = $columnName;

		if ( $restriction == null )
			$this->restriction = new DBEntryRestricion( -1 );
		else
			$this->restriction = $restriction;

		return $this;
	}

	/**
	 * Jakmile je dana trida ready, aplikuje se akce
	 * Pokud neni definovany zadny trigger, nemuze byt aplikovana zadna akce
	 * @param type $state
	 * @return type 
	 */
	public function isReady($state)
	{
		if ( !is_null( $this->triggers ) )
		{
			switch ($state)
			{
				case self::$STATE_INSERT:

					if ( $this->isInBitmask( self::$IF_INSERT, $this->triggers ) )
					{
						if ( $this->checkAllOtherDependencies() == TRUE )
						{
							return TRUE;
						}
					}
					else
						return FALSE;
					break;

				case self::$STATE_UPDATE:
					if ( $this->isInBitmask( self::$IF_UPDATE, $this->triggers ) )
					{
						if ( $this->checkAllOtherDependencies() == TRUE )
						{
							return TRUE;
						}
					}
					else
						return FALSE;

					break;

				case self::$STATE_SAVE:
				default:
					if ( !$this->isInBitmask( self::$IF_INSERT, $this->triggers ) && !$this->isInBitmask( self::$IF_UPDATE, $this->triggers ) )
					{
						if ( $this->checkAllOtherDependencies() == TRUE )
						{
							return TRUE;
						}
					}
					else
						return FALSE;
					break;
				case self::$STATE_UPDATE_IGNORE_ALL:
					break;
			}
		} else
			return FALSE;
	}

	private function checkAllOtherDependencies()
	{
		if ( $this->isInBitmask( self::$IF_BOOLEAN, $this->triggers ) )
		{
			if ( $this->value != TRUE && $this->value != FALSE )
			{
				return FALSE;
			}
		}

		if ( $this->isInBitmask( self::$IF_NOT_BOOLEAN, $this->triggers ) )
		{
			if ( $this->value == TRUE OR $this->value == FALSE )
			{
				return FALSE;
			}
		}

		if ( $this->isInBitmask( self::$IF_NULL, $this->triggers ) )
		{
			if ( $this->value != null )
			{
				return FALSE;
			}
		}

		if ( $this->isInBitmask( self::$IF_NOT_NULL, $this->triggers ) )
		{
			if ( $this->value == null )
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	public function __toString()
	{
		return $this->columnName;
	}

	public function lock($open = FALSE)
	{
		$this->is_locked = !$open;
	}

	public function is_locked()
	{
		return $this->is_locked;
	}

	public function hasAction()
	{
		return is_null( $this->action ) ? FALSE : TRUE;
	}

	/**
	 * @todo dopsat errory pro setValue!!
	 * @param int $checkState
	 * @return boolean 
	 */
	public function performAction($checkState = null)
	{
		if ( $checkState != null )
		{
			if ( !$this->isReady( $checkState ) )
				return FALSE;
		}

		switch ($this->actions)
		{
			case DBConditionEntry::$NEW_DATE:
				$this->setValue( date( 'Y-m-d' ) );
				break;
			case DBConditionEntry::$NEW_DATETIME:
				$this->setValue( date( 'Y-m-d H:i:s' ) );
				break;
			case DBConditionEntry::$NEW_VALUE:
			default:
				$this->set_value( $this->getArgumentFromAction( $this->actions ) );
				break;
		}
	}

	public function setValue($value)
	{
		if ( !$this->is_locked )
			if ( !is_null( $this->restriction ) )
			{
				if ( $this->restriction->hasRestrictions( DBEntryRestricion::$COL_LENGTH ) < strlen( $value ) && $this->restriction->hasRestrictions( DBEntryRestricion::$COL_LENGTH ) != -1 )
				{
					throw new DBConditionExceptions( DBConditionExceptions::$ERROR_COL_LENGTH, array($value, $this->restriction->hasRestrictions( DBEntryRestricion::$COL_LENGTH )) );
				}

				if ( $this->restriction->hasRestrictions( DBEntryRestricion::$IS_EMAIL ) )
				{

					if ( filter_var(filter_var($value, FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL) == FALSE )
					{
						throw new DBConditionExceptions( DBConditionExceptions::$ERROR_NOT_EMAIL, array($value) );
					}
				}

				if ( $this->restriction->hasRestrictions( DBEntryRestricion::$IS_INTEGER ) )
				{

					if ( is_numeric( $value ) == FALSE )
					{
						throw new DBConditionExceptions( DBConditionExceptions::$ERROR_NOT_NUMBER, array($value) );
					}
				}
			}
		$this->value = $value;
	}

	public function getValue()
	{
		if ( $this->current_state != self::$STATE_UPDATE_IGNORE_ALL && $this->restriction->hasRestrictions( DBEntryRestricion::$IS_NOT_NULL ) && ($this->value == null OR $this->value == '') )
		{
			throw new DBConditionExceptions( DBConditionExceptions::$ERROR_NOT_NULL, (string) $this );
		}

		return $this->value;
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

	private function getArgumentFromAction($string)
	{
		$pattern = '/(_\[|\[)(.*)\]$/';
		preg_match( $pattern, $string, $matches );

		if ( count( $matches ) == 3 )
			return $matches[2];
		else
			return false;
	}

	/**
	 * Meni TRUE na 1 a FALSE na 0 (mysql compatibility)
	 * @param Boolean $value
	 * @return integer 
	 */
	private function transformToBoolean($value)
	{
		if ( $value == TRUE OR $value == 1 )
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	private function isInBitmask($what, $where)
	{
		return ($where & $what) == $what ? TRUE : FALSE;
	}

	public function error()
	{
		$this->error = TRUE;
	}

	public function setCurrentState($state)
	{
		$this->current_state = $state;
	}

}

/* End of file DBConditionEntry.php */
/* Location: ./application/models/DatabaseModel/DBConditionEntry.php */

	
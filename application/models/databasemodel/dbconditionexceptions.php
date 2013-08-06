<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * DBConditionExceptions je errorova vrstva vstahujici se k DBConditionEntry tride
 * 
 * @name DBConditionExceptions
 * @author	Pavel Vais
 * @version 1.0
 * @copyright Pavel Vais
 * 
 */
class DBConditionExceptions extends Exception
{

	static $ERROR_COL_LENGTH = 'Text "%s" má více než %d znaků.';
	static $ERROR_NOT_NULL = 'Input "%s" nesmí být prázdný.';
	static $ERROR_DATA_NULL = 'Nastala neznámá chyba.';
	static $ERROR_NOT_DELETED = 'Pod daným ID nebyl nalezen žádný záznam.';
	static $ERROR_NOT_EMAIL = 'Záznam "%s" není platný email.';
	static $ERROR_NOT_NUMBER = 'Záznam "%s" není číslo.';
	public $error_message;

	/**
	 * KDY se podminka spusti a CO udela
	 * KDY: IF_UPDATE, IF_INSERT, IF_NULL, IF_BOOLEAN
	 * CO: NEW_DATE , NEW_DATETIME , VALUE
	 * @param type $name 
	 */
	public function __construct($type, $arguments = null)
	{

		if ($arguments == null)
		{
			$this->error_message = $type;
		}
		else
		{
			if (!is_array($arguments))
			{
				$arguments = array($arguments);
			}

			$this->error_message = vsprintf($type, $arguments);
		}
	}

	public function getErrorMessage()
	{
		return $this->error_message;
	}
}

/* End of file DBConditionExceptions.php */
/* Location: ./application/models/DatabaseModel/DBConditionExceptions.php */

	
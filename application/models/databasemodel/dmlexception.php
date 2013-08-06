<?php

/**
 * DMLException je chybova vrstva, která má za úkol
 * obstarávat všechny chyby, ktere se v DML stanou.
 *
 * @author Pavel Vais
 * @version 1.0
 */
class DMLException extends Exception
{

	const ERROR_COL_LENGTH = 'Text "%s" má více než %d znaků.';
	const ERROR_NOT_NULL = 'Input "%s" nesmí být prázdný.';
	const ERROR_DATA_NULL = 'Nastala neznámá chyba.';
	const ERROR_NOT_DELETED = 'Pod daným ID nebyl nalezen žádný záznam.';
	//static $ERROR_NOT_EMAIL = 'Záznam "%s" není platný email.';
	const ERROR_NOT_NUMBER = 'Záznam "%s" není číslo.';
	const ERROR_NOT_DATE = 'Záznam "%s" nesplňuje formát datumu.';

	const ERROR_NUMBER_NOT_FOUND = 401;
	const ERROR_NUMBER_DATA_NULL = 500;
	const ERROR_NUMBER_COL_LENGTH = 501;
	const ERROR_NUMBER_NOT_NULL = 502;
	const ERROR_NUMBER_NOT_NUMBER = 503;
	const ERROR_NUMBER_NOT_DATE = 504;
	const ERROR_NUMBER_NOT_DELETED = 505;
	
	/**
	 * Errorove cislo pokud se snazime dostat do databaze udaj, ktery
	 * z duplikacniho duvodu nelze vlozit. 
	 */
	const ERROR_NUMBER_DUPLICATED = 506;
	
	/**
	 * Cislo chyby, ktera nema jine cislo
	 */
	const ERROR_NUMBER_GENERIC = 599;

	private $error_message;
	public $code;

	/**
	 * 
	 * @param type $type - Chybova veta.
	 * @param String / Array(String) $arguments - predefinovane vety
	 * se mohou doplnit napriklad o  nazev sloupce a jine. To vse se
	 * urcuje pomoci tohoto parametru
	 */
	public function __construct($type, $code = self::ERROR_NUMBER_GENERIC, $arguments = null)
	{

		if ( $arguments == null )
		{
			$this->error_message = $type;
		}
		else
		{
			if ( !is_array( $arguments ) )
			{
				$arguments = array($arguments);
			}

			$this->error_message = vsprintf( $type, $arguments );
		}
		$this->code = $code;
	}

	public function getErrorMessage()
	{
		return $this->error_message;
	}

	public function getErrorCode()
	{
		return $this->code;
	}

}

?>

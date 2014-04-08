<?php
namespace DML;
if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * DMLhelper obsahuje nekolik statickych funkci, ktere usnadnuji
 * formulaci dat pri komunikaci s databazi
 * 
 * @name DMLHelper
 * @author	Pavel Vais
 * @version 1.0
 * @copyright Pavel Vais
 * @property CI_Loader $load
 * @property CI_DB_active_record $db
 */
class Helper
{

	/**
	 * Vrati aktualni cas kompatibilni s databazovym formatem
	 * @param boolean $time (ma se do aktualniho data zahrnout i cas?
	 * @return String 
	 */
	final static public function now($time = FALSE)
	{
		$format = "Y-m-d" . ($time ? " H:i:s" : "");
		return date( $format );
	}

	/**
	 * Pomoci sekund vrati datum ve spravnem MySQL formatu<br>
	 * @param int $seconds - epoch sekundy (mozne ziskat z time() )
	 * @return type 
	 */
	final static public function int2date($seconds)
	{
		$format = "Y-m-d H:i:s";
		return date( $format, $seconds );
	}

	/**
	 * Kontroluje spravnost emailu. 
	 * @param String $email
	 * @return boolean - TRUE pri spravnosti emailu, FALSE pri spatnem formatu
	 */
	final static public function check_email($email)
	{
		return preg_match( "/^[_a-z0-9-]+(\.[_a-z0-9+-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i", $email ) > 0 ? TRUE : FALSE;

		//return filter_var( $email, FILTER_VALIDATE_EMAIL ); // PHP >5.2
	}

	/**
	 * Zkontroluje, jestli je datum ve formatu DD-MM-YYYY with an optional HH:MM:SS
	 * @param String $string
	 * @return boolean 
	 */
	final static public function check_datetime($string)
	{
		return ( preg_match( '/\\A(?:^((\\d{2}(([02468][048])|([13579][26]))[\\-\\/\\s]?((((0?[13578])|(1[02]))[\\-\\/\\s]?((0?[1-9])|([1-2][0-9])|(3[01])))|(((0?[469])|(11))[\\-\\/\\s]?((0?[1-9])|([1-2][0-9])|(30)))|(0?2[\\-\\/\\s]?((0?[1-9])|([1-2][0-9])))))|(\\d{2}(([02468][1235679])|([13579][01345789]))[\\-\\/\\s]?((((0?[13578])|(1[02]))[\\-\\/\\s]?((0?[1-9])|([1-2][0-9])|(3[01])))|(((0?[469])|(11))[\\-\\/\\s]?((0?[1-9])|([1-2][0-9])|(30)))|(0?2[\\-\\/\\s]?((0?[1-9])|(1[0-9])|(2[0-8]))))))(\\s(((0?[0-9])|(1[0-9])|(2[0-3]))\\:([0-5][0-9])((\\s)|(\\:([0-5][0-9])))?))?$)\\z/', $string ) );
	}

	final static public function time_ago($time)
	{
		if ( is_numeric( $time ) == false )
		{
			$time = strtotime( $time );
		}

		$etime = time() - $time;

		if ( $etime < 0 )
			$prefix = "za";
		else
			$prefix = "před";

		if ( $etime <= 1 && $etime >= -1 )
		{
			return 'právě teď';
		}

		$a = array(12 * 30 * 24 * 60 * 60 => array('rok', 'roky', 'let', 'rokem', 'lety'),
			 30 * 24 * 60 * 60 => array('měsíc', 'měsíce', 'měsíců', 'měsícem', 'měsíci'),
			 7 * 24 * 60 * 60 => array('týden', 'týdny', 'týdnů', 'týdnem', 'týdny'),
			 24 * 60 * 60 => array('den', 'dny', 'dní', 'dnem', 'dny'),
			 60 * 60 => array('hodinu', 'hodiny', 'hodin', 'hodinou', "hodinami"),
			 60 => array('minutu', 'minuty', 'minut', "minutou", "minutami"),
			 1 => array('vteřinu', 'vteřiny', 'vteřin', "vteřinou", "vteřinami")
		);

		foreach ( $a as $secs => $str )
		{
			$d = abs( $etime / $secs );
			if ( $d >= 1 )
			{
				$r = round( $d );
				if ( $etime < 0 )
					return $prefix . ' ' . $r . ' ' . ($r == 1 ? $str[0] : ($r < 5 && $r > 0 ? $str[1] : $str[2]));
				else
					return $prefix . ' ' . $r . ' ' . ($r == 1 ? $str[3] : $str[4]);
			}
		}

		return false;
	}

	/**
	 * Z multipole ziska vzdy jen jednu hodnotu a tu pak vrati v poli.
	 * Napr.: chceme z pole([0] => array('id'=>1,....),[1] => .....)
	 * ziskat jen vsechny IDcka a nic jineho.
	 * @param Array $arrayOrObject - pole, z ktereho budeme
	 * informaci cerpat. (multipole muze byt pak array, nebo objekt, je to jedno
	 * Pozn.: Dany prvek nemusi nutne v danem poli existovat.
	 * @param type $searchKey - prvek v poli, z ktreho informaci ziskavame
	 * @return array
	 */
	static function getValuesFromArrays($arrayOrObject, $searchKey)
	{
		$return = array();

		if ( !is_array( $arrayOrObject ) || empty($arrayOrObject) )
			return $return;

		$isObject = is_object( $arrayOrObject[0] );

		foreach ( $arrayOrObject as $row )
		{
			if ( $isObject && isset( $row->{$searchKey} ) )
				$return[] = $row->{$searchKey};
			elseif ( isset( $row[$searchKey] ) )
				$return[] = $row[$searchKey];
		}

		return $return;
	}

}

/* End of file dmlhelper.php */
/* Location: ./application/models/dmlhelper.php */

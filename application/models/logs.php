<?php

//require_once('PasswordHash.php');

/**
 * Description of ConfirmModel
 * Tento model zajistuje generovani a nasledne checkovani potvrzovacich
 * retezcu.
 * Vhodne pro linky, ktere jsou pristupne jen docasne pro toho, kdo zna dany
 * hash
 * @author Pavel Vais
 */
class Logs
{
	/**
	 * HLAVNI Informacni konstanta 
	 * - muze se pouzit v log_treshold
	 */

	const INFO = 100;

	/**
	 * HLAVNI Uspesna konstanta 
	 * - muze se pouzit v log_treshold
	 */
	const SUCCESS = 200;

	/**
	 * HLAVNI Errorova konstanta 
	 * - muze se pouzit v log_treshold
	 */
	const ERROR = 500;

	/**
	 * HLAVNI Varovaci konstanta 
	 * - muze se pouzit v log_treshold
	 */
	const WARNING = 400;

	/**
	 * Array obsahujici vsechny constanty, ktere se MOHOU zapsat do db
	 * @var array
	 */
	static $log_treshold;

	/**
	 * Singleton codeigniteru
	 * @var Object
	 */
	static $ci;

	/**
	 * Nazev tabulky pro ukladani logu
	 * @var type 
	 */
	static $table_name;

	public static function init()
	{
		/* ============================
		 * SETTINGS							 *
		  ============================ */
		self::$ci = & get_instance();
		self::$table_name = "logs";
		self::$log_treshold = array(self::INFO, self::ERROR, self::WARNING);
	}

	/**
	 * Ulozi informacni log do systemu
	 * @param String $message - logujici zprava
	 * @param int $user_id[optional] - ůog se muze vstahovat k jednotlivemu uzivateli
	 * @param String $tag[optional] - Muze kategorizovat jednotlive logy 
	 * @return Boolean - true pri zapsani / false pri nezapsani
	 */
	static public function info($message, $user_id = null, $tag = null)
	{
		return self::save( $message, self::INFO, $user_id, $tag );
	}

	/**
	 * Ulozi warovny log do systemu
	 * @param String $message - logujici zprava
	 * @param int $user_id[optional] - ůog se muze vstahovat k jednotlivemu uzivateli
	 * @param String $tag[optional] - Muze kategorizovat jednotlive logy 
	 * @return Boolean - true pri zapsani / false pri nezapsani
	 */
	static public function warning($message, $user_id = null, $tag = null)
	{
		return self::save( $message, self::WARNING, $user_id, $tag );
	}

	/**
	 * Ulozi errorovy log do systemu
	 * @param String $message - logujici zprava
	 * @param int $user_id[optional] - ůog se muze vstahovat k jednotlivemu uzivateli
	 * @param String $tag[optional] - Muze kategorizovat jednotlive logy 
	 * @return Boolean - true pri zapsani / false pri nezapsani
	 */
	static public function error($message, $user_id = null, $tag = null)
	{
		return self::save( $message, self::ERROR, $user_id, $tag );
	}

	/**
	 * Ulozi uspesny log do systemu
	 * @param String $message - logujici zprava
	 * @param int $user_id[optional] - ůog se muze vstahovat k jednotlivemu uzivateli
	 * @param String $tag[optional] - Muze kategorizovat jednotlive logy 
	 * @return Boolean - true pri zapsani / false pri nezapsani
	 */
	static public function success($message, $user_id = null, $tag = null)
	{
		return self::save( $message, self::SUCCESS, $user_id, $tag );
	}

	/**
	 * Ulozi log do systemu
	 * @param String $message - logujici zprava
	 * @param int $type - Typ zpravy (100 = info ... atd)
	 * @param int $user_id[optional] - ůog se muze vstahovat k jednotlivemu uzivateli
	 * @param String $tag[optional] - Muze kategorizovat jednotlive logy 
	 * @return Boolean - true pri zapsani / false pri nezapsani
	 */
	static private function save($message, $type, $user_id = null, $tag = null)
	{
		if ( !self::can_log_it( $type ) )
			return false;

		$data = array(
			 'message' => $message,
			 'ip' =>  self::$ci->input->ip_address(),
			 'user_id' => $user_id,
			 'tag' => $tag,
			 'date' => DMLHelper::now(TRUE)
		);
		return self::$ci->db->insert( self::$table_name, $data );
	}

	static function backup($filename, $empty_table = false)
	{
		
	}

	/**
	 * Vrati vsechny logy
	 * @param type $page - offset u logu
	 * @param type $logs_count - pocet logu, ktere ma vratit
	 * @param int $user_id[optional] - Vrati logy pridruzene k danemu uctu
	 * @param int $status[optional] - Vrati logy pridruzene k danemu statusu
	 * @return type
	 */
	static function get_logs($page, $logs_count, $user_id = FALSE, $status = FALSE)
	{
		self::$ci->db->order_by( "date DESC" )
				  ->limit( $logs_count, $logs_count * ($page - 1) );
		self::_join_users();

		if ( $user_id != FALSE )
		{
			self::$ci->db->where( "user_id", $user_id );
		}

		if ( $status != FALSE )
		{
			self::$ci->db->where( "status", $status );
		}

		return self::$ci->db->get( self::$table_name );
	}
	
	/**
	 * Getter, ktery vrati bud uzivatelem zadanou hodnotu
	 * nebo hodnotu, kterou pozaduje funkce
	 * @param String $type
	 * @return String
	 */
	private function get_log_type($type)
	{
		return $this->log_type == null ? $type : $this->log_type;
	}

	/**
	 * @param type $type
	 */
	static private function delete_logs($type = null,$tags = null)
	{
		//TODO!!!!!
		self::$ci->db->where( "type", $type )
				  ->delete( self::$table_name );
	}

	/**
	 * Funkce zjisti, jestli se dany log muze zalogovat.
	 * @param type $type
	 * @return boolean
	 */
	static private function can_log_it($type)
	{
		foreach ( self::$log_treshold as $logtype )
		{
			if ( $type >= $logtype && $type < $logtype + 100 )
				return TRUE;
		}
		return FALSE;
	}

	static private function _join_users()
	{
		$this->db->join( "users", "users.id = logs.user_id", "left" );
		$this->db->select( "users.username,users.email" );
		return $this;
	}

}

?>

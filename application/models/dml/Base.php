<?php
namespace DML;
if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * DML neboli database model layer je abstraktni trida slouzici ke
 * komunikaci s databazi
 * Jako Abstraktni tridu je mozne DM pouzivat jako rodice pro dalsi tridy.
 * @changelog
 * 2.2 - + Pridana moznost debugovani do FB (self::$debug = TRUE)
 * 			+ Pridan prikaz dbDelete()
 * 			- Tabulka se nemuze zmenit na stejny nazev
 * 			- opraven bug v dbCountRows()
 * 			
 * 2.1 -
 * 		+ Předelana funkce dbJoin a dbJoinMN
 * 		- smazana funkionalita hooku, je to nesmysl :)
 *    - prepareSelect dan jako static funkce do DMLBuilderu
 * 2.0 - 
 *  - odstranena funkce cache. (neni za potrebi, je oddelena)
 *  - get() , get_one(), update() a save() prejmenovany na:
 * 		- dbGet(), dbGetOne(), dbUpdate() a dbSave()
 *  - pri vynulovani promenny staci dat add_data('sloupec',null);
 *  + pridana funkce dbJoin()
 *  + pridana funkce dbJoinMN()
 *  + pridana funkce filter
 *  + pridana funkce select
 *  + pridany hooky (na nich je zalozen dbJoin() a dbJoinMN()
 *  - Vytvoreni DMLTable se presunulo do tridy DMLBuilder.
 * 		+ pridana funkce DMLBuilder::loadTableInfo()		
 * 		+ pridana funkce DMLBuilder::buildTable()		
 *  + addData() - kdyz se vlozi null, null se vlozi i do updatu(insertu)
 * 
 * @name DML
 * @author	Pavel Vais
 * @version 2.2
 * @copyright Pavel Vais
 */

/**
 * @property CI_Loader $load
 * @property CI_DB_active_record $db
 * @property ConsoleLogger $consolelogger
 */
abstract class Base extends \CI_Model
{

	/**
	 * Odkazování na codeigniter
	 * @var CI_Controller 
	 */
	protected $ci;

	/**
	 * Odkazování na databazi codeigniteru
	 * @var CI_DB_active_record 
	 */
	protected $db;

	/**
	 * Trida, ktera zapouzdruje infromace z dane tabulky
	 * @var Table
	 */
	protected $tableInfo;

	/**
	 * Obsahuje hodnoty ke sloupcum, ktere se budou
	 * updatovat nebo insertovat
	 * @var array
	 */
	private $data;

	/**
	 * Sberna erroru. Pokud zadny neni, vrati se false
	 * - get_error()
	 * @var Array 
	 */
	private $error;

	/**
	 * Sberna dotazu do databaze vstahujici se k instanci teto tridy.
	 * @var Array 
	 */
	private $last_query = array();

	/**
	 * Nazev Tabulky. Nyni se nemusi volat pres get_table_name();
	 * @var type 
	 */
	protected $name;

	/**
	 * Uchovava vsechny selekty, ktere se pouziji
	 * @var type 
	 */
	protected $select;

	/**
	 * @var Array(DMLJoin)
	 */
	protected $joins;

	/**
	 * @var Array(DMLJoin)
	 */
	protected $joinsMN;

	/**
	 * Pokud je TRUE, neprovadi se zadna validace
	 * @var type 
	 */
	private $disableValidation = false;

	/**
	 * Pri aktivaci (true) se do FB vypisuji vsechny DB transakce
	 * @var boolean
	 */
	static $DEBUG = FALSE;

	/**
	 * Constructor teto tridy
	 */
	function __construct($table_name)
	{
		$this->ci = & get_instance();
		$this->name = $table_name;
		$this->cache = $this->ci->cache;
		$this->db = $this->ci->db;
		$this->tableInfo = Builder::loadTableInfo( $table_name );
		$this->joins = array();
		$this->joinsMN = array();
		$this->data = array();
		$this->select = array();

	}

	/**
	 * Ulozi data pro dalsi save / update
	 * @param array $data
	 * @return \DML 
	 */
	public function fetchData($data)
	{
		if ( is_object( $data ) )
			$data = get_object_vars( $data );

		$this->data = $data;
		return $this;
	}

	/**
	 * Prida jednotliva data a stavajici nepremaze.
	 * @param String / array $data - bud slouzi jako array nebo
	 * jako klic, do ktereho se prida hodnota value (oznacuje nazev slupce
	 * @param String $value - hodnota, ktera se prida do nazvu sloupce ($data)
	 * @return \DML 
	 */
	protected function addData($data, $value = null)
	{
		if ( is_array( $data ) )
		{
			$this->data = array_merge( $this->data, $data );
		}
		else
		{
			$this->data[$data] = $value;
		}

		return $this;
	}

	protected function removeData($data)
	{
		if ( isset( $this->data[$data] ) )
		{
			unset( $this->data[$data] );
		}
		return $this;
	}

	/**
	 * Ziska vsechny data z databaze. Pokud je nastaven nejaky join,
	 * prida se k vysledku
	 * @return boolean
	 */
	protected function dbGet()
	{
		$this->proceedJoins();
		//$this->proceedSelects();

		$result = $this->db->get( $this->name );

		$this->save_last_query();
		$this->log_operation( $result );

		if ( $result->num_rows() == 0 )
			return false;

		$result = $result->result();

		//= Je aktivni nejaky postProcess?
		$this->proceedJoinsMN( $result );

		return $result;
	}

	/**
	 * Vrati jeden radek z tabulky. 
	 * Pokud dany radek neexistuje, vraci se FALSE
	 * @return boolean FALSE pri nezdaru / vysledek (Object)  
	 */
	protected function dbGetOne()
	{
		$this->db->limit( 1 );

		//= Projeti vsech PRE HOOKU
		$this->proceedJoins();
		//$this->proceedSelects();

		$result = $this->db->get( $this->name );
		$this->save_last_query();
		$this->log_operation( $result );

		if ( $result->num_rows() != 1 )
			return FALSE;
		$this->proceedJoinsMN( $result->row() );
		return $result->row();
	}

	/**
	 * Smaze vsechny radky, ktere jsou ovlivneny WHERE parametrem
	 * @return \DML
	 */
	protected function dbDelete()
	{
		$this->db->delete( $this->name );
		$this->save_last_query();
		return $this;
	}

	/**
	 * 
	 * @param type $table - nazev tabulky, kterou chcete do vysledku napojit
	 * @param type $fromColumn - pokud ma tabulka vice sloupcu, ktere
	 * na danou tabulku odkazuji, je potreba, abyste manualne urcili,
	 * kterou tabulku chcete propojit
	 * @return DMLJoinMN
	 */
	public function &dbJoinMN($table, $fromColumn = null)
	{

		$this->joinsMN[] = new JoinMN( $table, $this->tableInfo, $fromColumn );

		return $this->joinsMN[count( $this->joinsMN ) - 1];
	}

	/**
	 * 
	 * @param type $table - nazev tabulky, kterou chcete do vysledku napojit
	 * @param type $fromColumn - pokud ma tabulka vice sloupcu, ktere
	 * na danou tabulku odkazuji, je potreba, abyste manualne urcili,
	 * kterou tabulku chcete propojit
	 * @param string $fromTable - pokud chcete pouzit join z jiny tabulky nez 
	 * z te, ktera je prave aktivni, tak vyuzijete treti parametr
	 * (hodi se na vnoreny join, ktery vyuziva predesly join)
	 * @return Join
	 */
	public function &dbJoin($table, $fromColumn = null, $fromTable = null)
	{
		$targetTable = $this->tableInfo;

		if ( $fromTable != null )
		{
			$targetTable = Builder::loadTableInfo( $fromTable );
		}

		$this->joins[] = new Join( $table, $targetTable, $fromColumn );

		return $this->joins[count( $this->joins ) - 1];
	}

	private function proceedJoins()
	{
		if ( empty( $this->joins ) )
			return;
		$this->db->select( Builder::prepareSelect( $this->select, $this->name ) );
		$identificator = 0;
		foreach ( $this->joins as $join )
		{
			$identificator++;
			$fromColumn = $join->referencingColumn;
			if ( ($pos = strpos( $fromColumn, '_' )) === strpos( $fromColumn, '_id' ) )
			{
				$join->execute( $this->db, $identificator > 1 ? $identificator : ''  );
				continue;
			}

			$findTable = substr( $fromColumn, 0, $pos );
			$join->referencingColumn = substr( $fromColumn, $pos + 1 );
			foreach ( $this->joins as $j )
			{
				if ( rtrim( $j->getGeneratedInfo( 'AS' ), '_' ) == $findTable )
				{
					$join->execute( $this->db, $identificator > 1 ? $identificator : '', $j->getGeneratedInfo( 'tableName' ), $fromColumn );
					continue 2;
				}
			}
		}
	}

	private function proceedJoinsMN(&$result)
	{
		foreach ( $this->joinsMN as $joinMN )
		{
			$joinMN->execute( $this->db, $result );
		}
	}

	/**
	 * Prida seznam sloupcu, ktery se vyfiltruji. Aplikuji se jako posledni vec pred dotazem.
	 * Diky tomu se spravne naformatuje i join a neztrati se zadny sloupce.
	 * @param type $columns
	 * @param type $overwrite - predesle selecty se vynuluji
	 * @param type $add_prefix - prida ke sloupcum i prefix
	 * 	- TRUE = prida prefix s touto tabulkou
	 * 	- FALSE = neprida prefix ke sloupcum
	 * 	- JINA HODNOTA (String) = prida se presne tato hodnota
	 * @return \DML
	 * @deprecated since version 2.0
	 */
	public function select($columns, $overwrite = false, $add_prefix = false)
	{
		if ( !is_array( $columns ) )
		{
			$columns = explode( ',', $columns );

			foreach ( $columns as &$col )
			{
				$col = trim( $col, ' ' );
			}
		}
		if ( $overwrite )
			$this->select = $columns;
		else
		{
			$this->select = array_merge( $this->select, $columns );
			$this->select = array_unique( $this->select );
		}

		if ( $add_prefix != FALSE )
		{
			$this->select = Builder::prepareSelect( $this->select, $add_prefix == true ? $this->name : $add_prefix  );
		}

		return $this;
	}

	/**
	 *  Vrati pocet radku v tabulce
	 */
	protected function dbCountRows()
	{
		$this->db->select( 'COUNT(*) as pocet', FALSE );
		$result = $this->dbGetOne();
		$this->sendDebugMessage( 'počet vrácených řádků: ' . $result->pocet, 'dbCountRows' );
		return $result->pocet;
	}

	/**
	 * Tato funkce automaticky rozeznava, jestli ma provest insert nebo update.
	 * 
	 * Pokud je pres fetchData prenesena ID hodnota, pak se provede update,
	 * jinak save()
	 * @return boolean 
	 */
	protected function save()
	{
		if ( $this->data == null )
		{
			$error = new DBException( DBException::ERROR_DATA_NULL, DBException::ERROR_NUMBER_DATA_NULL );
			$this->set_error( $error->getErrorMessage(), $error->getCode() );
			$this->db->_reset_write();
			$this->deleteAllData();

			return FALSE;
		}

		$method_insert = TRUE;
		if ( isset( $this->data[$this->tableInfo->primary_column] ) &&
				  $this->data[$this->tableInfo->primary_column] !== 0
		)
			$method_insert = FALSE;

		if ( !$this->disableValidation )
		{
			\Autoloader::$finder->find(__NAMESPACE__.'\ValidatorInterface');
			if ( $method_insert )
				$validator = new ValidatorInsert();
			else
				$validator = new ValidatorUpdate();


			$validator->set_data( $this->tableInfo, $this->data );
			try
			{
				$validator->validate();
			}
			catch (DBException $exc)
			{
				$this->set_error( $exc->getErrorMessage(), $exc->getCode() );
				$this->db->_reset_write();
				$this->deleteAllData();

				return FALSE;
			}
		}

		if ( $method_insert )
		{
			$this->db->set( $this->data );
			$this->db->insert( $this->name );
			$this->log_operation();
			$this->save_last_query();
		}
		else
		{
			//= Nastavi se sql where na primarni sloupec, ktery se nasledne vyjme z data, aby se neduplikoval
			$this->db->where( $this->tableInfo->primary_column, $this->data[$this->tableInfo->primary_column] );
			unset( $this->data[$this->tableInfo->primary_column] );

			$this->db->set( $this->data );
			$this->db->update( $this->tableInfo->get_table_name() );
			$this->save_last_query();
			$this->log_operation();
		}
		$this->deleteAllData();
		return TRUE;
	}

	/**
	 * Provede nekolikanasobny insert. Data musi byt strukturovany
	 * [0] -> data k vlozeni
	 * [1] -> data k vlozeni
	 * ...
	 * @return boolean - TRUE - povedlo se, FALSE - nepovedlo 
	 */
	protected function save_batch()
	{
		if ( $this->data == null )
		{
			$error = new DBException( DBException::ERROR_DATA_NULL, DBException::ERROR_NUMBER_DATA_NULL );
			$this->set_error( $error->getErrorMessage(), $error->getCode() );
			$this->db->_reset_write();
			$this->deleteAllData();
			return FALSE;
		}

		$validator = new ValidatorInsert();
		if ( !$this->disableValidation )
		{
			try
			{
				foreach ( $this->data as $data )
				{
					$validator->set_data( $this->tableInfo, $data );
					$validator->validate();
				}
			}
			catch (DBException $exc)
			{
				$this->set_error( $exc->getErrorMessage(), $exc->getCode() );
				$this->db->_reset_write();
				$this->deleteAllData();
				return FALSE;
			}
		}

		$this->db->insert_batch( $this->tableInfo->get_table_name(), $this->data );
		$this->save_last_query();
		$this->log_operation();
		$this->deleteAllData();
		return TRUE;
	}

	/**
	 * Tato funkce se musi volat, pokud se neprovadi insert nebo update
	 * dle primary sloupce.
	 * TZN.: kdyz chci updatnout vsechny knihy s titulkem "dummy", zavolam tuto
	 * funkci. Validator se zapne pouze pro aktivni sloupce
	 */
	public function update()
	{
		if ( !$this->disableValidation )
		{
			$validator = new ValidatorUpdate();
			$validator->set_data( $this->tableInfo, $this->data );

			try
			{
				$validator->validate();
			}
			catch (DBException $exc)
			{
				$this->set_error( $exc->getErrorMessage(), $exc->getCode() );
				$this->db->_reset_write();
				return FALSE;
			}
		}

		$this->db->set( $this->data );
		$this->db->update( $this->tableInfo->get_table_name() );
		$this->save_last_query();
		$this->log_operation();
		$this->deleteAllData();
		return $this->db->affected_rows();
	}

	/**
	 * Vrati hodnoty a pripravi tridu pro dalsi
	 * volani prikazu 
	 */
	public function clear()
	{
		$this->data = null;
		$this->disableValidation = false;
		return $this;
	}

	/**
	 * Vsechny budouci dotazy se nastavi na transakci
	 * (pro uspesnou operaci s databazi se musi po 
	 * skonceni vsech potrenych dotazu ukoncit "stop_transaction")
	 * Pokud se neco nepovede, vsechno se navrati funkci "rollback_transaction"
	 * POZOR!: jedna se o singleton, pokud v jedne instanci povolite transakce,
	 * pote se to projevi i v jinych instancich
	 * @return \DML
	 */
	public function start_transaction()
	{
		$this->db->trans_begin();
		$this->sendDebugMessage( 'Transakce byla spustena.', 'Transaction' );
		return $this;
	}

	/**
	 * Odsouhlasi transakci a vse se zapise do databaze
	 * @return \DML
	 */
	public function stop_transaction()
	{
		$this->db->trans_commit();
		$this->sendDebugMessage( 'Transakce byla odsouhlasena.', 'Transaction' );
		return $this;
	}

	/**
	 * Neodsouhlasi transakci a vse bude navraceno zpet
	 * @return \DML
	 */
	public function rollback_transaction()
	{
		$this->db->trans_rollback();
		$this->sendDebugMessage( 'Transakce byla zamítnuta.', 'Transaction' );
		return $this;
	}

	/**
	 * Vymaze cachovane informace o tabulce
	 * Pri dalsim updatu / insertu se znova vytvori 
	 */
	public function cache_table_clear()
	{
		$this->cache->delete( $this->cache_prefix . 'table_' . $this->tableInfo->get_table_name() );
	}

	public function last_id()
	{
		return $this->db->insert_id();
	}

	/**
	 * Ulozi do historie posledni dotaz na databazi 
	 */
	private function save_last_query()
	{
		$this->last_query[] = $this->db->last_query();
		$this->sendDebugMessage( $this->db->last_query(), get_class( $this ) );
	}

	/**
	 * Vrati ulozeny dotaz s databazi dle indexu.
	 * Pokud parametr neni zadan, vrati se POSLEDNI query
	 * @param int $index - Pokud se $index = -1, pote se vrati
	 * cela historie, jinak se vrati dle idnexu. Pokud pod danym
	 * indexem nic neni, vrati se null.
	 * @return String
	 */
	public function get_queries_history($index = 0)
	{
		if ( $index == -1 )
		{
			//= Vrati se vsechny dotazy
			return $this->last_query;
		}

		return isset( $this->last_query[$index] ) ? $this->last_query[$index] : null;
	}

	/**
	 * Vrati hodnotu, kterou jste do tridy vlozili pomoci fetch_data() nebo
	 * add_data().
	 * @param String $data_name - definovani hodnoty, kterou chcete navratit
	 * @return String / null
	 */
	public function getDataValue($data_name)
	{
		return isset( $this->data[$data_name] ) ? $this->data[$data_name] : null;
	}

	/**
	 * Smaze z vlozenych dat urcenych pro insert nebo update hodnotu.
	 * @param String $data_name - nazev polozkys
	 * @return \DML 
	 */
	public function deleteDataByName($data_name)
	{
		unset( $this->data[$data_name] );
		return $this;
	}

	/**
	 * Vymaze vsechny data, vlozena do modelu za ucelem ulozeni nebo upraveni.
	 * Vola se autoamticky po volani funkce save, save_batch a update.
	 * Automaticky se zas nastavi escapovani hodnot
	 * @return \DML 
	 */
	public function deleteAllData()
	{
		$this->data = array();
		$this->escape = TRUE;
		return $this;
	}

	/**
	 * Prida limit k dotazu.
	 * @param type $page - jaka stranka? Pocita se od jednicky
	 * @param type $per_page - kolik dotazu na stranku se ma zobrazit
	 * @return \DML
	 */
	public function page($page, $per_page = 10)
	{
		$this->db->limit( $per_page, $per_page * ($page - 1) );
		return $this;
	}

	/**
	 * Zapise chybu
	 * @param int $code
	 * @param String $message
	 * @return \DML 
	 */
	protected function set_error($message, $code = DBException::ERROR_NUMBER_GENERIC)
	{
		$this->error = array(
			 'message' => $message,
			 'code' => $code
		);
		$this->sendDebugMessage( $message, 'Error!' );
		return $this;
	}

	/**
	 * Vrátí errorovou hlášku
	 * @return String 
	 */
	public function get_error_message()
	{
		return isset( $this->error['message'] ) ? $this->error['message'] : null;
	}

	/**
	 * Vrátí cislo chyby
	 * @return String 
	 */
	public function get_error_code()
	{
		return isset( $this->error['code'] ) ? $this->error['code'] : null;
	}

	/**
	 * Do consoleLoggeru prida dalsi radek z db
	 * @param type $result
	 * @return boolean
	 */
	protected function log_operation($result = null)
	{
		if ( ENVIRONMENT != 'development' )
			return false;

		if ( !$result instanceof CI_DB_mysql_result )
			$result = null;

		$callers = debug_backtrace();

		$par_func[0] = $callers[2]['class'] . "::" . $callers[2]['function'] . "() " . (isset( $callers[2]['line'] ) ? "ln " . $callers[2]['line'] : "");
		$par_func[1] = (isset( $callers[3]['class'] ) ? $callers[3]['class'] . "::" : "") . $callers[3]['function'] . "() " . (isset( $callers[3]['line'] ) ? "ln " . $callers[3]['line'] : "");

		$this->ci->consolelogger->set_namespace( "database" )
				  ->set_data( "query", $this->db->last_query() )
				  ->set_data( 'parent_function', $par_func )
				  ->set_data( 'elapsed_time', $this->db->elapsed_time() )
				  ->set_data( 'rows', $result == null ? $this->db->affected_rows() : $result->num_rows()  )
				  ->set_data( 'result', $result == null ? null : $result->result()  )
				  ->new_row();
	}

	/**
	 * Vrati logy
	 */
	public function get_logs()
	{
		$this->ci->consolelogger->get_data_from_namespace( 'database' );
	}

	/**
	 * Povoli nebo zakaze validaci pri update a save
	 * @param type $disable
	 * @return \DML
	 */
	public function disableValidation($disable = true)
	{
		$this->disableValidation = $disable;
		return $this;
	}

	/**
	 * Kolik radku bylo ovlivneno prikazem
	 * save() a update();
	 * @return type
	 */
	public function affected_rows()
	{
		return $this->db->affected_rows();
	}

	/**
	 * Vnori selekty do databazove vrstvy
	 */
	private function proceedSelects()
	{
		/*
		 * Pokud neni zadny select, vytvori se TABLE_NAME.*
		 */
		if ( empty( $this->select ) )
			$this->db->select( $this->name . '.*' );

		else
		{
			$this->db->select( $this->select );
		}
	}

	protected function change_table($table_name)
	{
		if ( $table_name != $this->name )
		{
			$this->tableInfo = Builder::loadTableInfo( $table_name );
			$this->name = $table_name;
			$this->sendDebugMessage( 'Byla změněna tabulka na ' . $table_name, 'Change Table' );
		}
		return $this;
	}

	/**
	 * Vypise informace o tabulce (debug usefull)
	 * @return string 
	 * @debug-only
	 */
	public function get_table_info_string()
	{
		$string = "";
		if ( $this->tableInfo->is_columns_cached() )
		{
			$string .= "<ul>" . PHP_EOL;
			foreach ( $this->tableInfo->get_columns() as $column_name => $column )
			{
				$string .= "<li><strong>$column_name</strong> - type: " . $column['type'] .
						  ($column['length'] > 0 ? "(" . $column['length'] . ")" : "") .
						  " null " . ($column['is_nullable'] ? "YES" : "NO") .
						  " primary " . ($column['is_primary'] ? "YES" : "NO");
			}
			$string .= "</ul>";
		}
		else
		{
			$string = "table " . $this->tableInfo->get_table_name() . " is not cached.";
		}

		return $string;
	}

	private function sendDebugMessage($message, $label)
	{
		if ( self::$DEBUG && ENVIROMENT == "development" )
			\FB::info( $message, '(DML) ' . $label );
		return $this;
	}

}

/* End of file DML.php */
/* Location: ./application/models/DML.php */

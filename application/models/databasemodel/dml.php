<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * DML neboli database model layer je abstraktni trida slouzici ke
 * komunikaci s databazi
 * Jako Abstraktni tridu je mozne DM pouzivat jako rodice pro dalsi tridy.
 * @changelog
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
 *  - Vytvoreni DMLTable se presunulo do tdy DMLBuilder.
 * 		+ pridana funkce DMLBuilder::loadTableInfo()		
 * 		+ pridana funkce DMLBuilder::buildTable()		
 * 
 * 
 * @name DML
 * @author	Pavel Vais
 * @version 2.0
 * @copyright Pavel Vais
 */

/**
 * @property CI_Loader $load
 * @property CI_DB_active_record $db
 * @property ConsoleLogger $consolelogger
 */
abstract class DML extends CI_Model
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
	 * @var DMLTable
	 */
	protected $tableInfo;

	/**
	 * Hooky, ktery se provedou PRED (PRE)
	 * nebo PO (POST) vykonani GET prikazu
	 * @var type 
	 */
	protected $hooks = array();

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
	 * Pokud je TRUE, neprovadi se zadna validace
	 * @var type 
	 */
	private $disableValidation = false;

	/**
	 * Constructor teto tridy
	 */
	function __construct($table_name)
	{
		Autoloader::loadStatic( 'models/databasemodel/dmlbuilder', 'DMLBuilder' );
		$this->ci = & get_instance();
		$this->name = $table_name;
		$this->cache = $this->ci->cache;
		$this->db = $this->ci->db;
		$this->tableInfo = DMLBuilder::loadTableInfo( $table_name );

		$this->data = array();
		$this->select = array();
		$this->hooks = array(
			 'PRE' => array(),
			 'POST' => array()
		);

		//= pridani hooku na validaci selectu
		$this->hooks['PRE'][] = array(
			 'type' => 'SELECT'
		);

		include_once APPPATH . "models/databasemodel/dmlvalidatorinterface.php";
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
			$this->data = array_merge( $this->data, $data );
		else
			$this->data[$data] = $value;
		
		return $this;
	}

	protected function removeData($data)
	{
		if ( isset( $this->data[$data] ) )
			unset( $this->data[$data] );
		return $this;
	}

	/**
	 * Ziska vsechny data z databaze. Pokud je nastaven nejaky join,
	 * prida se k vysledku
	 * @return boolean
	 */
	protected function dbGet()
	{

		//= Projeti vsech PRE HOOKU
		$this->proceedPreProcessing();

		$result = $this->db->get( $this->name );
		$this->log_operation( $result );

		if ( $result->num_rows() == 0 )
			return false;

		$result = $result->result();

		//= Je aktivni nejaky postProcess?
		$this->proceedPostProcessing( $result );

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
		$this->proceedPreProcessing();

		$result = $this->db->get( $this->tableInfo->get_table_name() );
		$this->save_last_query();
		$this->log_operation( $result );

		if ( $result->num_rows() != 1 )
			return FALSE;

		$this->proceedPostProcessing( $result->result() );
		return $result->row();
	}

	/**
	 * Propoji MN jednu tabulku s druhou. S tim e vysledky se zaradi do promenny
	 * ktera ma stejny nazev jako dana tabulka.
	 * @param String $table - nazev pozadovane tabulky
	 * @param String $select - select jednotlivych sloupcu v tabulce
	 *  NULL = vemou se vsechny sloupce
	 * @param Boolean $escape - ma se select escapovat?
	 * @param function $function - muze se pouzit anonymni funkce pro upresneni 
	 * dotazu, vhodne pro seskupovani vysledku napr.:
	 * function(){
				$this->db->group_by('authors_id');
			});
	 */
	public function dbJoinMN($table, $select = null, $escape = true, $function = false)
	{

		$this->hooks['POST'][] = array(
			 'type' => 'JOINMN',
			 'arg' => $table,
			 'select' => $select,
			 'escape' => $escape,
			 'eval' => $function
		);
	}

	public function dbJoin($table, $select = null)
	{
		$this->hooks['PRE'][] = array(
			 'type' => 'JOIN',
			 'arg' => $table,
			 'select' => $select
		);
	}

	/**
	 * Funkce na obslouzeni PRE hooku a zavolani spravnych funkci
	 * @param type $result
	 * @return boolean
	 */
	private function proceedPreProcessing()
	{
		$hooks = $this->hooks['PRE'];
		$hooks = array_reverse( $hooks );
		if ( count( $hooks ) == 0 )
			return false;

		foreach ( $hooks as $process )
		{
			switch ($process['type'])
			{
				case 'JOIN':
					$this->proceedJoinConnections( $process );
					break;
				case 'SELECT':
					$this->proceedSelectHook();
					break;
			}
		}
	}

	/**
	 * Funkce na obslouzeni POST hooku a zavolani spravnych funkci
	 * @param type $result
	 * @return boolean
	 */
	private function proceedPostProcessing(&$result)
	{
		$hooks = $this->hooks['POST'];

		if ( count( $hooks ) == 0 )
			return false;

		foreach ( $hooks as $process )
		{
			switch ($process['type'])
			{
				case 'JOINMN':
					$this->proceedJoinMNConnections( $process, $result );
					break;
			}
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
			$this->prepareSelect( $this->select, $add_prefix == true ? $this->name : $add_prefix  );

		return $this;
	}

	/**
	 *  Vrati pocet radku v tabulce
	 */
	protected function dbCountRows()
	{
		$this->db->select( 'COUNT(*) as pocet', FALSE );
		$result = $this->get_one();
		$this->save_last_query();
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
			$error = new DMLException( DMLException::ERROR_DATA_NULL, DMLException::ERROR_NUMBER_DATA_NULL );
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
			if ( $method_insert )
				$validator = new DMLValidatorInsert();
			else
				$validator = new DMLValidatorUpdate();


			$validator->set_data( $this->tableInfo, $this->data );
			try
			{
				$validator->validate();
			}
			catch (DMLException $exc)
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
			$error = new DMLException( DMLException::ERROR_DATA_NULL, DMLException::ERROR_NUMBER_DATA_NULL );
			$this->set_error( $error->getErrorMessage(), $error->getCode() );
			$this->db->_reset_write();
			$this->deleteAllData();
			return FALSE;
		}

		$validator = new DMLValidatorInsert();
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
			catch (DMLException $exc)
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
			$validator = new DMLValidatorUpdate();
			$validator->set_data( $this->tableInfo, $this->data );

			try
			{
				$validator->validate();
			}
			catch (DMLException $exc)
			{
				$this->set_error( $exc->getErrorMessage(), $exc->getCode() );
				$this->db->_reset_write();
				return FALSE;
			}
		}

		$this->db->set( $this->data, $this->escape );
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
		return $this;
	}

	/**
	 * Odsouhlasi transakci a vse se zapise do databaze
	 * @return \DML
	 */
	public function stop_transaction()
	{
		$this->db->trans_commit();
		return $this;
	}

	/**
	 * Neodsouhlasi transakci a vse bude navraceno zpet
	 * @return \DML
	 */
	public function rollback_transaction()
	{
		$this->db->trans_rollback();
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

	public function cache_clear()
	{
		
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
	protected function set_error($message, $code = DMLException::ERROR_NUMBER_GENERIC)
	{
		$this->error = array(
			 'message' => $message,
			 'code' => $code
		);

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
	private function proceedSelectHook()
	{
		/*
		 * Pokud neni zadny select, vytvori se TABLE_NAME.*
		 */
		if ( empty( $this->select ) )
			$this->db->select( $this->name . '.*' );

		else
		{
			FB::info( $this->select, 'pridavam select' );
			$this->db->select( $this->select );
		}
	}

	/**
	 * Pripravi Join na pozadovanou tabulku
	 * @param type $process
	 */
	private function proceedJoinConnections($process)
	{
		$tableName = $this->name;
		/**
		 * Zjistim, jakej sloupecek vlastne musim propojit
		 */
		if ( ($referencingColumn = $this->tableInfo->has_this_table_referencing( $process['arg'] )) == FALSE )
		{
			show_error( 'Join(): Tabulka ' . $tableName . ' neodkazuje na tabulku ' . $process['arg'] );
		}
		/**
		 * Nyni musime ziskat nazev sloupecku, na ktery tabulka odkazuje
		 */
		$targetTableInfo = DMLBuilder::loadTableInfo( $process['arg'] );
		$targetColumn = $targetTableInfo->has_foreign_table_referencing( $tableName );

		//= Nyni poresime select
		if ( is_null( $process['select'] ) )
		{
			$columns = $targetTableInfo->get_columns_names();
			unset( $columns[$targetColumn] ); //= Nemusime znova stahovat IDcko, ktere uz je v cizim klici
		}
		else
			$columns = $process['select'];

		//= Nemame jistotu, ze se nejake sloupce nebudou prekryvat, radsi 
		//= vsechny vlozeny zprefixujeme jejich tabulkou
		$this->select = $this->prepareSelect( $this->select, $this->name );


		$columns = $this->prepareSelect( $columns, $process['arg'], $process['arg'] . '_' );
		$this->db->select( $columns )
				  ->join( $process['arg'], $process['arg'] . '.' . $targetColumn . ' = ' . $tableName . '.' . $referencingColumn );
	}

	/**
	 * Pripravi MN Join
	 * @param type $hook
	 */
	private function proceedJoinMNConnections($hook, &$result)
	{
		FB::info( $result, 'RESULT' );
		$mainTable = $hook['arg'];
		$dataWhere = array();
		$myTables = $this->tableInfo->get_incoming_foreign_data();
		$foreignTableInfo = DMLBuilder::loadTableInfo( $mainTable );
		foreach ( $myTables as $column => $tables )
		{
			//= Musime ziskat vsechny WHERE argumenty z resultu
			foreach ( $result as $rows )
			{
				if ( !isset( $rows->$column ) )
					show_error( 'proceedJoinMNConnections(): v SELECTu neni sloupec ' . $column );
				$dataWhere[] = $rows->$column;
			}
			foreach ( $tables as $table )
			{

				$targetColumn = $this->tableInfo->get_table_name() . '_' . $column;
				$targetTable = $table;
				// Nyni vim co hledat, jdu na to!
				//= Mrknu do BOOKS - stahnu vscnhy IN
				//= je tam books_list?
				if ( ($mainColumn = $foreignTableInfo->has_foreign_table_referencing( $targetTable )) !== false )
				{
					//= Musime vytvorit radnej select
					if ( $hook['escape'] )
					{
						$select = $this->prepareSelect( $hook['select'], $mainTable );
						$select[] = $targetColumn;
					}
					else
					{
						$select = $hook['select'];
						$select .= ',' . $targetColumn;
					}



					//= Vime, ze tato tabulka ($targetTable) je prostrednikem!!
					$this->db->join( $targetTable, $targetTable . '.' . $targetColumn . '=' . $this->tableInfo->get_table_name() . '.' . $column )
							  ->join( $mainTable, $mainTable . '.' . $mainColumn . '=' . $targetTable . '.' . $mainTable . '_' . $mainColumn )
							  ->select( $select, $hook['escape'] )
							  ->where_in( $this->tableInfo->get_table_name() . '.' . $column, $dataWhere );

					if ( $hook['eval'] != false && is_callable( $hook['eval'] ) )
						$hook['eval']();

					$foreignResult = $this->db->get( $this->name );

					$this->log_operation( $foreignResult );

					break 2;
				}
			}
			show_error( 'dbJoinMN(): Neexistuje zadna MN reference z tabulky ' . $this->name . ' na tabulku ' . $mainTable );
		}
		//= Nyni musime sjednotit foreignResult s resultem
		$foreignResult = $foreignResult->result();
		foreach ( $result as &$row )
		{
			foreach ( $foreignResult as $k => &$r )
			{
				if ( $row->$column == $r->$targetColumn )
				{
					unset( $r->$targetColumn );
					$row->{$mainTable}[] = $r;
					unset( $foreignResult[$k] );
				}
			}

			if ( !isset( $row->{$mainTable} ) )
				$row->{$mainTable} = false;
		}
		FB::info( $result );
	}

	/**
	 * Vsem selektum to prida prefix a vrati array
	 * @param Array/String $select
	 * @param String $tablePrefix
	 * @return Array
	 */
	private function prepareSelect($select, $tablePrefix, $AS_prefix = null)
	{

		if ( $select == null )
			return array($tablePrefix . '.*');

		if ( !is_array( $select ) )
			$select = explode( ',', $select );
		foreach ( $select as &$s )
		{
			$s = (strpos( $s, '.' ) === FALSE ? $tablePrefix . '.' . trim( $s, ' ' ) : $s) . ($AS_prefix != null ? ' AS ' . $AS_prefix . $s : '');
		}

		return $select;
	}

	protected function change_table($table_name)
	{
		$this->tableInfo = DMLBuilder::loadTableInfo( $table_name );
		$this->name = $table_name;
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

}

/* End of file DatabaseModel.php */
/* Location: ./application/models/DatabaseModel.php */

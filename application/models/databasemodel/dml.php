<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * DML neboli database model layer je abstraktni trida slouzici ke
 * komunikaci s databazi
 * Jako Abstraktni tridu je mozne DM pouzivat jako rodice pro dalsi tridy.
 * 
 * @name DML
 * @author	Pavel Vais
 * @version 1.1
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
	 * Cachovaci system ze tridy Cache
	 * @var Cache
	 */
	protected $cache;

	/**
	 * Obal pro cachovani sql dotazu
	 * @var DMLCache
	 */
	private $query_cache;

	/**
	 * Trida, ktera zapouzdruje infromace z dane tabulky
	 * @var DMLTable
	 */
	protected $table_info;

	/**
	 * Prefix k souborum vstahujici se k dml
	 * @var String 
	 */
	private $cache_prefix = 'dml_';

	/**
	 * Obsahuje hodnoty ke sloupcum, ktere se budou
	 * updatovat nebo insertovat
	 * @var array
	 */
	private $data;

	/**
	 *
	 * @var Array 
	 */
	private $error;

	/**
	 * Sberna dotazu do databaze vstahujici se k instanci teto tridy.
	 * @var Array 
	 */
	private $last_query = array();

	/**
	 * $escape signalizuje, jestli se update nebo save provede
	 * s excapovanyma datama nebo ne.
	 * Pro vyssi bezpecnost nepouzivat, jen v pripade
	 * ze opravdu vite co delate.
	 * @var boolean
	 */
	private $escape = TRUE;

	/**
	 * Tato konstanta zaruci, ze se vlozi hodnota NULL do 
	 * updatu, nebo savu. 
	 * Pokud se vlozi string NULL, tak se dana hodnota smaze,
	 * Timto se zaruci, ze se vlozi hodnota NULL
	 */

	const NULL_VALUE = '@null';

	/**
	 * Constructor teto tridy
	 */
	function __construct($table_name)
	{

		$this->ci = & get_instance();
		$this->ci->load->library( 'cache' );
		$this->data = array();
		$this->cache = $this->ci->cache;
		$this->db = $this->ci->db;
		$this->table_info = new DMLTable( $table_name );
		include_once APPPATH . "models/databasemodel/dmlvalidatorinterface.php";
	}

	/**
	 * Ulozi data pro dalsi save / update
	 * @param array $data
	 * @return \DML 
	 */
	public function fetch_data($data)
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
	public function add_data($data, $value = null)
	{
		if ( is_array( $data ) )
			$this->data = array_merge( $this->data, $data );

		if ( !is_null( $value ) )
		{
			if ( $value === 0 )
				$this->data[$data] = 0;
			else
				$this->data[$data] = ($value === self::NULL_VALUE ? null : $value);
		}
		return $this;
	}

	protected function get()
	{
		if ( $this->query_cache != null )
		{

			$result = $this->query_cache->get();

			if ( $result == false )
			{

				$result = $this->db->get( $this->table_info->get_table_name() );

				if ( $result->num_rows() > 0 )
				{
					$result = $result->result();

					$this->query_cache->save( $result );
				}
				else
				{
					return FALSE;
				}
			}
			else
			{
				$this->db->_reset_select();
			}
		}
		else
		{
			$result = $this->db->get( $this->table_info->get_table_name() );

			$this->log_operation( $result );

			if ( $result->num_rows() == 0 )
				return false;
			else
				return $result->result();
		}

		$this->clear_query_cache();
		return $result;
	}

	/**
	 * Vrati jeden radek z tabulky. Na tento prikaz
	 * se nevstahuje zadne cachovani.
	 * Pokud dany radek neexistuje, vraci se FALSE
	 * @return boolean FALSE pri nezdaru / vysledek (Object)  
	 */
	protected function get_one()
	{
		$this->db->limit( 1 );
		$result = $this->db->get( $this->table_info->get_table_name() );
		$this->save_last_query();
		$this->log_operation( $result );
		return $result->num_rows() == 1 ? $result->row() : FALSE;
	}

	/**
	 *  Vrati pocet radku v tabulce
	 */
	protected function count_rows()
	{
		$this->db->select( 'COUNT(*) as pocet', FALSE );
		$result = $this->get_one();
		$this->save_last_query();
		return $result->pocet;
	}

	/**
	 * Tato funkce automaticky rozeznava, jestli ma provest insert nebo update.
	 * 
	 * Pokud je pres fetch_data prenesena 
	 * @return boolean 
	 */
	protected function save()
	{
		//= pokud neni cachovani, podiva se jestli case existuje
		//= pokud ne, tak se vytvori nova
		if ( !$this->table_info->is_columns_cached() )
		{
			if ( $this->get_cached_table_info() == false )
			{
				$this->build_table();
			}
		}
		if ( $this->data == null )
		{
			$error = new DMLException( DMLException::ERROR_DATA_NULL, DMLException::ERROR_NUMBER_DATA_NULL );
			$this->error = array(
				 'message' => $error->getErrorMessage(),
				 'code' => $error->getCode()
			);
			$this->db->_reset_write();
			$this->delete_all_data();

			return FALSE;
		}

		$method_insert = TRUE;
		if ( isset( $this->data[$this->table_info->primary_column] ) &&
				  $this->data[$this->table_info->primary_column] !== 0
		)
			$method_insert = FALSE;
		if ( $method_insert )
			$validator = new DMLValidatorInsert();
		else
			$validator = new DMLValidatorUpdate();


		$validator->set_data( $this->table_info, $this->data );
		try
		{
			$validator->validate();
		}
		catch (DMLException $exc)
		{
			$this->error = array(
				 'message' => $exc->getErrorMessage(),
				 'code' => $exc->getCode()
			);
			$this->db->_reset_write();
			$this->delete_all_data();

			return FALSE;
		}


		if ( $method_insert )
		{
			$this->db->set( $this->data, $this->escape );
			$this->db->insert( $this->table_info->get_table_name() );
			$this->log_operation();
			$this->save_last_query();
		}
		else
		{
			//= Nastavi se sql where na primarni sloupec, ktery se nasledne vyjme z data, aby se neduplikoval
			$this->db->where( $this->table_info->primary_column, $this->data[$this->table_info->primary_column] );
			unset( $this->data[$this->table_info->primary_column] );

			$this->db->set( $this->data, $this->escape );
			$this->db->update( $this->table_info->get_table_name() );
			$this->save_last_query();
			$this->log_operation();
		}
		$this->delete_all_data();
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
		//= pokud neni cachovani, podiva se jestli case existuje
		//= pokud ne, tak se vytvori nova
		if ( !$this->table_info->is_columns_cached() )
		{
			if ( $this->get_cached_table_info() == false )
			{
				$this->build_table();
			}
		}

		if ( $this->data == null )
		{
			$error = new DMLException( DMLException::ERROR_DATA_NULL, DMLException::ERROR_NUMBER_DATA_NULL );
			$this->error = array(
				 'message' => $error->getErrorMessage(),
				 'code' => $error->getCode()
			);
			$this->db->_reset_write();
			$this->delete_all_data();
			return FALSE;
		}

		$validator = new DMLValidatorInsert();

		try
		{
			foreach ( $this->data as $data )
			{
				$validator->set_data( $this->table_info, $data );
				$validator->validate();
			}
		}
		catch (DMLException $exc)
		{
			$this->error = array(
				 'message' => $exc->getErrorMessage(),
				 'code' => $exc->getCode()
			);
			$this->db->_reset_write();
			$this->delete_all_data();
			return FALSE;
		}

		$this->db->insert_batch( $this->table_info->get_table_name(), $this->data );
		$this->save_last_query();
		$this->log_operation();
		$this->delete_all_data();
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
		//= pokud neni cachovani, podiva se jestli cache existuje
		//= pokud ne, tak se vytvori nova
		if ( !$this->table_info->is_columns_cached() )
		{
			if ( $this->get_cached_table_info() == false )
			{
				$this->build_table();
			}
		}
		if ( $this->escape )
		{
			$validator = new DMLValidatorUpdate();
			$validator->set_data( $this->table_info, $this->data );

			try
			{
				$validator->validate();
			}
			catch (DMLException $exc)
			{
				$this->error = array(
					 'message' => $exc->getErrorMessage(),
					 'code' => $exc->getCode()
				);
				$this->db->_reset_write();
				return FALSE;
			}
		}
		
		$this->db->set( $this->data, $this->escape );
		$this->db->update( $this->table_info->get_table_name() );
		$this->save_last_query();
		$this->log_operation();
		$this->delete_all_data();
		return $this->db->affected_rows();
	}

	/**
	 * Aktivuje pro nasledujici select 
	 * @param DMLCache $cache 
	 */
	protected function query_cache_activation(DMLCache $cache)
	{
		$this->query_cache = $cache;
		$this->query_cache->assignCache( $this->cache );
		$this->query_cache->set_name( $this->table_info->get_table_name() );
	}

	/**
	 * Invaliduje cache s danymi tagy. Vhodne pri pouziti 
	 * updatu a insertu.<br>
	 * Pri invalidovani bez tagu se invalidujou vsechny
	 * case spojene s timto modelem
	 * @param DMLCache $cache 
	 * @param boolean $cache_group - invaliduje vsechny cache, ktery maji dane tagy
	 */
	protected function query_cache_invalide(DMLCache $cache, $cache_group = FALSE)
	{
		$this->query_cache_activation( $cache );
		$this->query_cache->invalide( $cache_group );
		$this->clear_query_cache();
		return $this;
	}

	/**
	 * Vymaze cachovani dotazu
	 *  - vyuziva se po selectu, aby se cachovani neaplikovalo na dalsi dotaz
	 */
	private function clear_query_cache()
	{
		$this->query_cache = null;
	}

	private function get_table_spec()
	{
		//if ($this->table_info)
	}

	/**
	 * Precte si to pozadavky z tabulky, ulozi do cache a  
	 */
	private function build_table()
	{
		//= Aby se provedl dotaz, musi se momentalni ulozit do promenne a smazat.
		//= Pote se zase obnovi. Tim nedojde k naruseni dotazu.
		$stored_session = $this->db->store_session();
		$this->db->_reset_select();


		$this->db->select( 'COLUMN_NAME, DATA_TYPE, IS_NULLABLE,  CHARACTER_MAXIMUM_LENGTH,COLUMN_KEY' )
				  ->where( 'table_name', $this->table_info->get_table_name() )
				  ->where( 'table_schema', $this->db->database )
				  ->group_by( 'COLUMN_NAME' )
				  ->order_by( 'ORDINAL_POSITION' );
		$result = $this->db->get( 'INFORMATION_SCHEMA.COLUMNS' );
		$this->save_last_query();

		if ( $result->num_rows() == 0 )
			show_error( 'DML: Pri parsovani tabulky vznikla chyba: tabulka ' . $this->table_info->get_table_name() . ' neexistuje.' );

		foreach ( $result->result() as $column )
		{
			$this->table_info->add_column( $column->COLUMN_NAME, $column->DATA_TYPE, $column->CHARACTER_MAXIMUM_LENGTH, $column->IS_NULLABLE == "YES" ? true : false, $column->COLUMN_KEY == DMLTable::COL_PRIMARY ? true : false  );
		}

		$result = $this->cache->write( $this->table_info, $this->cache_prefix . 'table_' . $this->table_info->get_table_name() );

		//= Navraceni stareho dotazu, ktery se muze po vytvoreni cache zpracovat
		$this->db->restore_session( $stored_session );

		return $this;
	}

	/**
	 * Vrati hodnoty a pripravi tridu pro dalsi
	 * volani prikazu 
	 */
	public function clear()
	{
		$this->data = null;

		return $this;
	}

	/**
	 * Vrati zacachovane informace o tabulce
	 * @return DMLTable - pri neuspechu vraci FALSE 
	 */
	public function get_cached_table_info()
	{
		$a = $this->cache->get( $this->cache_prefix . 'table_' . $this->table_info->get_table_name() );
		if ( $a !== FALSE )
		{
			$this->table_info = $a;
			return $this->table_info;
		}
		else
			return false;
	}

	/**
	 * Vymaze cachovane informace o tabulce
	 * Pri dalsim updatu / insertu se znova vytvori 
	 */
	public function cache_table_clear()
	{
		$this->cache->delete( $this->cache_prefix . 'table_' . $this->table_info->get_table_name() );
	}

	public function cache_clear()
	{
		
	}

	public function last_id()
	{
		return $this->db->insert_id();
	}

	/**
	 * Vypise informace o tabulce (debug usefull)
	 * @return string 
	 */
	public function get_table_info_string()
	{
		$string = "";
		$this->table_info = $this->get_cached_table_info();
		if ( $this->table_info->is_columns_cached() )
		{
			$string .= "<ul>" . PHP_EOL;
			foreach ( $this->table_info->get_columns() as $column_name => $column )
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
			$string = "table " . $this->table_info->get_table_name() . " is not cached.";
		}

		return $string;
	}

	/**
	 * Vraci poslední použitý dotaz
	 * @return String 
	 */
	public function last_query()
	{
		return $this->get_queries_history( count( $this->last_query ) - 1 );
	}

	/**
	 * Vrati ulozeny dotaz s databazi dle indexu
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

		return isset( $this->last_query[$index] ) ? $this->last_query : null;
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
	 * Vrati hodnotu, kterou jste do tridy vlozili pomoci fetch_data() nebo
	 * add_data().
	 * @param String $data_name - definovani hodnoty, kterou chcete navratit
	 * @return String / null
	 */
	public function get_data_value($data_name)
	{
		return isset( $this->data[$data_name] ) ? $this->data[$data_name] : null;
	}

	/**
	 * Smaze z vlozenych dat urcenych pro insert nebo update hodnotu.
	 * @param String $data_name - nazev polozkys
	 * @return \DML 
	 */
	public function delete_data_by_name($data_name)
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
	public function delete_all_data()
	{
		$this->data = array();
		$this->escape = TRUE;
		return $this;
	}

	/**
	 * Ulozi do historie posledni dotaz na databazi 
	 */
	private function save_last_query()
	{
		$this->last_query[] = $this->db->last_query();
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

	public function get_logs()
	{
		$this->ci->consolelogger->get_data_from_namespace( 'database' );
	}

	/**
	 * Tato funkce vypne (TRUE) nebo zapne (FALSE)
	 * escapovani vlozenych dat, ktere se spracuji
	 * ve funkci save() nebo update()
	 * @param boolean $disable
	 * @return \DML 
	 */
	public function escape_values($enable = TRUE)
	{
		$this->escape = $enable;
		return $this;
	}
	
	public function affected_rows()
	{
		return $this->db->affected_rows();
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

}

/* End of file DatabaseModel.php */
/* Location: ./application/models/DatabaseModel.php */

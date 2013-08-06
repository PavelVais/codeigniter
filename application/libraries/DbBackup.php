<?php

/**
 * Description of Comments
 *
 * @author Pavel Vais
 * @property CI_Loader $load
 * @property CI_DB_active_record $db
 * @property CI_DB_utility $dbutil
 * @property CI_Zip $zip
 */
class DbBackup
{

	private $backup_path = "backup/";
	private $current_backup;
	private $backup_tables_separately = false;
	private $tables = array();

	function __construct()
	{
		$this->ci = & get_instance();

		$this->ci->load->library( 'zip' );
		$this->ci->load->helper( 'file' );
	}

	/**
	 * Vytvori zalohu databaze. Muzete potom pouzit prikaz
	 * save_as_zip nebo jen save()
	 * @param type $savename_after_backup [null] - pokud je urcena, zaloha se ihned
	 * s danym jmenem ulozi
	 * @param type $add_drop_table [TRUE] - prida drop table syntaxi do zalohy
	 * @param type $add_insert_data [TRUE] - prida vsechny data, nejen strukturu
	 * tabulek
	 * @return \DbBackup
	 */
	public function backup($savename_after_backup = NULL, $add_drop_table = TRUE, $add_insert_data = TRUE)
	{
		$this->ci->load->dbutil();

		if ( $this->backup_tables_separately )
		{
			$this->current_backup = array();

			foreach ( count( $this->tables ) == 0 ? $this->get_tables() : $this->tables as $table )
			{
				$prefs = array(
					 'tables' => $table, // Array of tables to backup.
					 'ignore' => array(), // List of tables to omit from the backup
					 'format' => 'txt', // gzip, zip, txt
					 'add_drop' => $add_drop_table, // Whether to add DROP TABLE statements to backup file
					 'add_insert' => $add_insert_data, // Whether to add INSERT data to backup file
					 'newline' => "\r\n" // Newline character used in backup file
				);

				// Backup your entire database and assign it to a variable
				$this->current_backup[] = array(
					 "filename" => $table,
					 "content" => $this->ci->dbutil->backup( $prefs )
				);
			}
		}
		else
		{
			$prefs = array(
				 'tables' => $this->tables, // Array of tables to backup.
				 'ignore' => array(), // List of tables to omit from the backup
				 'format' => 'txt', // gzip, zip, txt
				 'add_drop' => $add_drop_table, // Whether to add DROP TABLE statements to backup file
				 'add_insert' => $add_insert_data, // Whether to add INSERT data to backup file
				 'newline' => "\r\n" // Newline character used in backup file
			);

			// Backup your entire database and assign it to a variable
			$this->current_backup = & $this->ci->dbutil->backup( $prefs );
		}

		if ( $savename_after_backup != null )
			$this->save( $savename_after_backup );

		return $this;
	}

	public function save($backup_name)
	{
		if ( $this->current_backup == null )
			show_error( "DbBackup error: Nebyla vytvorena zadna zaloha, ktera by se mohla ulozit." );

		if ( is_array( $this->current_backup ) )
		{
			
			foreach ( $this->current_backup as $table )
			{
				$fileName = date( 'Y-m-d-Hi' ) . "_dbbackup_$backup_name"."_".$table["filename"].".txt";
				write_file( $this->get_backup_path() . $fileName, $table["content"] );
			}
		}
		else
		{
			write_file( $this->get_backup_path() . $backup_name . ".txt" , $this->current_backup);
		}
		
	}

	public function download($backup_name = null)
	{
		$this->ci->load->helper( 'download' );

		if ( $backup_name == null )
		{
			if ( $this->current_backup == null )
				show_error( "DbBackup error: Nebyla vytvorena zadna zaloha, ktera se muze stahnout." );
			else
				$data = $this->current_backup;
		} else
		{
			$data = file_get_contents( $this->get_backup_path() . $backup_name );
		}

		force_download( $backup_name, $data );
	}

	public function save_and_download($backup_name)
	{
		
	}

	public function save_as_zip($backup_name)
	{
		$fileName = date( 'Y-m-d-Hi' ) . "_dbbackup_$backup_name.zip";
		if ( $this->backup_tables_separately )
		{
			foreach ( $this->current_backup as $table )
			{
				$this->ci->zip->add_data( $table["filename"] . ".txt", $table["content"] );
			}

			$this->ci->zip->archive( $this->get_backup_path() . $fileName );
		}
		else
		{
			if ( $this->current_backup == null )
				show_error( "DbBackup error: Nebyla vytvorena zadna zaloha, ktera by se mohla ulozit." );


			$this->ci->zip->add_data( $backup_name . ".txt", $this->current_backup );

			$this->ci->zip->archive( $this->get_backup_path() . $fileName );
		}

		return $this;
	}

	/**
	 * Vsechny tabulky z databaze vypise do samostatnych souboru.
	 * Tato moznost se hodi z pravidla pri aplikovani pouze
	 * nekterych tabulek ze zalohy (napr. ze zipu)
	 * @param type $enable - zapne nebo vypne tuto vlastnost
	 * @return \DbBackup 
	 */
	public function write_data_separately($enable = TRUE)
	{
		$this->backup_tables_separately = $enable;

		return $this;
	}

	/**
	 * Oznaci tabulky, ktere se maji zazalohovat
	 * @param String/array $data - nazvy tabulek
	 * @return \DbBackup 
	 */
	public function select_tables($data)
	{
		$this->tables = array();

		if ( !is_array( $data ) )
			$data = array($data);

		foreach ( $data as $table )
		{
			$this->tables[] = $table;
		}

		return $this;
	}

	/**
	 * Vrati seznam vsech zaloh
	 * @return array 
	 */
	public function get_backups()
	{
		$this->ci->load->helper('directory');
		return directory_map($this->get_backup_path());
	}

	/**
	 * Nastavi cilovou slozku pro zalohu
	 * @param String $path 
	 */
	public function set_backup_path($path)
	{
		$this->backup_path = trim( $path, "/" ) . "/";
	}

	
	public function apply_backup($name, $backup_before = FALSE)
	{
		
	}

	/**
	 * Vrati cestu k zaloze
	 * @param boolean $absolute_path - TRUE - vrati absolutni cestu
	 * FALSE - vrati relativni cestu
	 * @return type 
	 */
	public function get_backup_path($absolute_path = FALSE)
	{
		return $absolute_path ? site_url( $this->backup_path ) : $this->backup_path;
	}

	/**
	 * Vrati seznam vsceh tabulek z databaze
	 * @return array
	 */
	public function get_tables()
	{
		return $this->ci->db->list_tables();
	}

}

?>

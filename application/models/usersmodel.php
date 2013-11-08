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
class UsersModel extends DML
{
	/**
	 * Jmeno sloupce, dle ktereho se 
	 * kontroluje email.
	 */

	const EMAIL_CHECK = "email";

	/**
	 * Jmeno sloupce, dle ktereho se 
	 * kontroluje, jestli dane ID
	 * jiz neexistuje. 
	 */
	const ID_CHECK = "id";

	/**
	 * Jmeno sloupce, podle ktereho 
	 * se kontroluje, jestli uz nahodou
	 * neexistuje dany facebook ucet
	 *
	 */
	const FACEBOOK_ID_CHECK = "fcb_id";

	/**
	 * Jmeno sloupce, ktery kontroluje,
	 * jestli neni dany username obsazeny 
	 */
	const USERNAME_CHECK = "username";
	const FACEBOOK_TYPE = "facebook";
	const NORMAL_TYPE = "internal";
	const ROWS_PER_PAGE = 10;

	/**
	 * Konstruktor tridy,
	 * nacte helper string. 
	 */
	public function __construct()
	{
		//= Nastaveni tabulky users
		parent::__construct( 'users' );
	}

	public function get_user_by_id($id)
	{
		$this->db->where( "id", $id );
		return $this->dbGetOne();
	}

	public function get_user_by_username($username)
	{
		$this->db->where( "username", $username );
		return $this->dbGetOne();
	}

	public function count_users($facebook_users = FALSE)
	{
		if ( $facebook_users )
			$this->db->where( "fcb_id > ", 0 );
		return $this->count_rows();
	}

	public function is_user_avaible($value, $type = self::USERNAME_CHECK)
	{
		$this->db->select( "id" );
		$this->db->where( $type, $value );
		return $this->dbGetOne() == false ? true : false;
	}

	public function search_user($search_term)
	{
		$this->db->like( 'LOWER(email)', strtolower( $search_term ) )
				  ->or_like( 'LOWER(username)', strtolower( $search_term ) )
				  ->select( 'username,email,id' )
				  ->limit( 5 );
		return $this->dbGet();
	}

	public function login_via_facebook($facebook_id)
	{
		$this->load->library( 'session' );
		$this->db->select( "username, email, id" );
		$this->db->where( "fcb_id", $facebook_id );
		$result = $this->dbGetOne();
		if ( $result == false )
			return false;

		$this->ci->session->set_userdata( array(
			 'user_id' => $result->id,
			 'username' => $result->username == "" ? $result->name . " " . $result->surname : $result->username,
			 'status' => "1",
		) );

		$this->update_login_info( $result->id, true, true );

		return $result;
	}

	/**
	 * 
	 * @param type $id
	 * @return type 
	 */
	function update($id, $type = self::ID_CHECK)
	{
		$this->db->where( $type, $id );
		return parent::update();
	}

	public function activate_user($id, $activate = TRUE)
	{
		$this->addData( "activated", $activate ? 1 : 0  )
				  ->addData( "id", $id );
		return $this->save();
	}

	public function change_role($id, $role)
	{
		return $this->addData( "role", $role )
							 ->addData( "id", $id )
							 ->save();
	}

	/**
	 * Zabanuje nebo odbanuje dany ucet
	 * @param int $id - id usera
	 * @param boolean $ban - ma se ucet zabanovat nebo odbanovat
	 * @param boolean $reason - duvod zabanovani
	 */
	public function ban_user($id, $ban = TRUE, $reason = null)
	{
		$this->fetch_data( array(
			 'id' => $id,
			 'banned' => $ban ? 1 : 0,
			 'ban_reason' => $ban ? $reason : ""
		) );

		return $this->save();
	}

	function save_user($data = null)
	{
		if ( $data != null )
			$this->fetch_data( $data );
		return $this->save();
	}

	function create_user($username, $fcb_id = null, $email = null, $password = null, $activated = TRUE, $role = "registered")
	{
		if ( $fcb_id != null )
		{
			$this->fetch_data( array(
				 'username' => $username,
				 'email' => $email,
				 'fcb_id' => $fcb_id,
				 'role' => $role,
				 'last_ip' => $this->ci->input->ip_address(),
				 'last_login' => DMLHelper::now( TRUE ),
				 'created' => DMLHelper::now( TRUE ),
				 'activated' => 1
			) );
		}
		else
		{
			// Hash password using phpass
			$hasher = new PasswordHash( 8, FALSE );
			$hashed_password = $hasher->HashPassword( $password );

			$this->fetch_data( array(
				 'username' => $username,
				 'password' => $hashed_password,
				 'email' => $email,
				 'last_ip' => $this->ci->input->ip_address(),
				 'last_login' => DMLHelper::now( TRUE ),
				 'created' => DMLHelper::now( TRUE ),
				 'activated' => $activated ? 1 : 0
			) );
		}

		return $this->save();
	}

	function get_all_users($page = 1)
	{
		if ($page != false)
			$this->db->limit( self::ROWS_PER_PAGE, self::ROWS_PER_PAGE * ($page - 1) );
		
		return $this->dbGet();
	}

	function filter_users($keywords, $where_array, $page = 1, $just_count_it = false)
	{
		if ( $keywords !== "" )
		{
			$this->db->like( "LOWER(username)", strtolower( $keywords ) )
					  ->or_like( "LOWER(email)", strtolower( $keywords ) )
					  ->or_like( "LOWER(role)", strtolower( $keywords ) );
		}
		if ( $where_array !== null )
		{
			$this->db->where($where_array);
		}
		
		if ($just_count_it)
			return $this->count_users();
		
		$this->db->limit( self::ROWS_PER_PAGE, self::ROWS_PER_PAGE * ($page - 1 ) );

		return $this->dbGet();
	}

	/**
	 * Vrati cislo facebook uctu. Pokud se k danemu idcku zadny facebook ucet
	 * nevstahuje, vrati FALSE
	 * @param int $id
	 * @return boolean 
	 */
	function get_facebook_id($id)
	{
		$this->db->where( "id", $id )
				  ->select( "fcb_id" );

		$result = $this->dbGetOne();
		return $result == FALSE ? FALSE : $result->fcb_id;
	}

	/**
	 * Propoji facebook ucet s normalnim uctem
	 * @param type $username - username (login) normalniho uctu
	 * @param type $fcb_id - id facebook uctu
	 * @return boolean 
	 */
	function link_users($first_id, $second_id)
	{
		$fcb_id = $this->get_facebook_id( $first_id );
		$fcb_id2 = $this->get_facebook_id( $second_id );

		if ( $fcb_id + $fcb_id2 == 0 )
		{
			$this->set_error( "Facebook účet nebyl ani na jednom účtu nalezen. Nic se nespojilo.", 511 );
			return false;
		}

		if ( $fcb_id > 0 )
		{
			$fcb_id_official = $fcb_id;
			$target_id = $second_id;
			$source_id = $first_id;
		}
		else
		{
			$fcb_id_official = $fcb_id2;
			$target_id = $first_id;
			$source_id = $second_id;
		}

		$this->db->where( "fcb_id", $fcb_id_official );
		$result = $this->dbGetOne();

		if ( $result != false )
		{
			$this->db->where( "id", $target_id );
			$this->addData( "fcb_id", $fcb_id_official );
			$this->addData( "surname", $result->surname );
			if ( $result->email != null )
				$this->addData( "email", $result->email );


			$status = $this->update();
		} else
		{
			$this->set_error( "Facebook účet nebyl nalezen.", 511 );
			return false;
		}

		if ( $status === 0 )
		{
			//$this->set_error( "Normální účet nebyl nalezen", 512 );
			return false;
		}
		if ( $status != FALSE )
		{
			$this->db->where( "id", $source_id );
			$this->db->delete( $this->tableInfo->get_table_name() );
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Get user record by email
	 *
	 * @param	string
	 * @return	object
	 */
	function get_user_by_email($email)
	{

		$this->db->where( 'LOWER(email)=', strtolower( $email ) );
		return $this->dbGetOne();
	}

	function get_user_by_facebook_id($facebook_id)
	{
		$this->db->where( self::FACEBOOK_ID_CHECK, $facebook_id );
		return $this->dbGetOne();
	}

	/**
	 * Update user login info, such as IP-address or login time, and
	 * clear previously generated (but not activated) passwords.
	 *
	 * @param	int
	 * @param	bool
	 * @param	bool
	 * @return	void
	 */
	function update_login_info($user_id, $record_ip, $record_time, $type = self::ID_CHECK)
	{
		$this->addData( "new_password_key", null );
		$this->addData( "new_password_requested", null );


		if ( $record_ip )
			$this->addData( "last_ip", $this->input->ip_address() );
		if ( $record_time )
			$this->addData( "last_login", date( 'Y-m-d H:i:s' ) );

		if ( $type == self::ID_CHECK )
			$this->db->where( 'id', $user_id );
		else
			$this->db->where( 'fcb_id', $user_id );

		return $this->update( $user_id, $type );
	}

	public function password_hash($password)
	{
		$hasher = new PasswordHash( 8, FALSE );
		return $hasher->HashPassword( $password );
	}

	public function login($login, $column)
	{
		$this->db->where( 'LOWER(' . $column . ')=', strtolower( $login ) );
		return $this->dbGetOne();
	}

	/**
	 * Zmeni heslo uzivateli s ID $user_id, Tato funkce nevyzaduje nic jineho.
	 * Paklize meni heslo uzivatel, je vhodne vyuzit funkci
	 * change_password_safe()
	 * @param int $user_id
	 * @param String $password
	 * @return type
	 */
	public function change_password($user_id, $password)
	{
		$this->addData( "id", $user_id )
				  ->addData( "password", $password );

		$this->db->where( "id", $user_id );
		return $this->save();
	}

	public function change_password_safe($user_id, $new_password, $old_password)
	{
		$this->addData( "id", $user_id )
				  ->addData( "password", $new_password );

		$this->db->where( "id", $user_id )
				  ->where( "password", $old_password );
		$this->save();
		if ( $this->affected_rows() > 1 )
			return TRUE;
		else
			return FALSE;
	}

	public function change_value($id, $column, $value)
	{
		return $this->addData( $column, $value )
							 ->update( $id, self::ID_CHECK );
	}

}

?>

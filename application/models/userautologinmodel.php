<?php

/**
 * @author Pavel Vais
 */
class UserAutologinModel extends DML\Base
{

	/**
	 * Konstruktor tridy,
	 * nacte helper string. 
	 */
	public function __construct()
	{
		//= Nastaveni tabulky users
		parent::__construct( 'user_autologin' );
	}

	/**
	 * Get user data for auto-logged in user.
	 * Return NULL if given key or user ID is invalid.
	 *
	 * @param	int
	 * @param	string
	 * @return	object
	 */
	function get($user_id, $key)
	{
		$this->db->select( 'users.id' )
				  ->select( 'users.username' )
				  ->select( 'users.email' )
				  ->join( 'users', $this->name . '.user_id = users.id' )
				  ->where( $this->name . '.user_id', $user_id )
				  ->where( $this->name . '.key_id', $key );
		return parent::dbGetOne();
	}

	/**
	 * Save data for user's autologin
	 *
	 * @param	int
	 * @param	string
	 * @return	bool
	 */
	function set($user_id, $key)
	{
		$this->addData( 'user_id', $user_id )
				  ->addData( 'key_id', $key )
				  ->addData( 'user_agent', substr( $this->input->user_agent(), 0, 149 ) )
				  ->addData( 'last_ip', $this->input->ip_address() );
		return $this->save( true );
	}

	/**
	 * Delete user's autologin data
	 *
	 * @param	int
	 * @param	string
	 * @return	void
	 */
	function delete($user_id, $key)
	{
		$this->db->where( 'user_id', $user_id )
				  ->where( 'key_id', $key );
		$this->dbDelete();
	}

	/**
	 * Delete all autologin data for given user
	 *
	 * @param	int
	 * @return	void
	 */
	function clear($user_id)
	{
		$this->db->where( 'user_id', $user_id );
		$this->dbDelete();
	}

	/**
	 * Purge autologin data for given user and login conditions
	 *
	 * @param	int
	 * @return	void
	 */
	function purge($user_id)
	{
		$this->db->where( 'user_id', $user_id )
				  ->where( 'user_agent', substr( $this->input->user_agent(), 0, 149 ) )
				  ->where( 'last_ip', $this->input->ip_address() );
		$this->dbDelete();
	}

}

?>

<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of dummyModel
 *
 * @author Daw
 */
class ConfessionModel extends DML
{

	const ROWS_PER_PAGE = 15;

	public function __construct()
	{

		parent::__construct( 'confessions' );
	}

	public function get_by_id($confession_id)
	{
		$this->db->where( "confessions.id", $confession_id );
		return $this->_join_hashtags()->get_one();
	}

	public function get_new()
	{
		$this->db->where( "seen", 0 )
				  ->order_by( "created ASC" );

		return $this->_join_hashtags()->get();
	}

	public function get_old($limit = null)
	{
		$this->db->where( "seen", 1 )
				  ->order_by( "created DESC" );

		if ( $limit != null )
			$this->db->limit( $limit );
		return $this->_join_hashtags()->get();
	}

	public function get_deleted($limit = null)
	{
		$this->db->where( "seen", -1 )
				  ->order_by( "created DESC" );

		if ( $limit != null )
			$this->db->limit( $limit );

		return $this->_join_hashtags()->get();
	}

	private function _join_hashtags()
	{
		$this->db->join( "hashtags", "hashtags.id = confessions.hashtag_id", "left" )
				  ->select( "CONCAT('#',value) as hashtag,text,created,seen,confessions.id as id, hashtag_id", FALSE );
		return $this;
	}

	public function page($page = 1)
	{
		$this->db->limit( self::ROWS_PER_PAGE, self::ROWS_PER_PAGE * ($page - 1) );
		return $this;
	}

	public function edit($confession_id, $text, $hashtag)
	{
		$hm = new HashtagsModel();

		$old = $this->get_by_id( $confession_id );

		if ( !$old )
			return false;

		$hash_id = $hm->rename( $old->hashtag, $hashtag );
		$this->add_data( "hashtag_id", $hash_id );

		$this->db->where( "id", $confession_id );
		$this->add_data( "text", $text )
				  ->update();

		if ( $old->hashtag != null )
		//= Smazeme stary hashtag z db (jestli uz neni vyuzivan)
			$hm->delete_safe( $old->hashtag_id );

		return true;
	}

	public function add($text, $hashtag)
	{
		$hm = new HashtagsModel();

		if ( $hashtag != null )
		{
			$hash_id = $hm->add( HashtagsModel::prepare_hashtag( $hashtag ) );
			$this->add_data( "hashtag_id", ($hash_id ) );
		}
		$this->add_data( "text", $text );
		$this->add_data( "created", DMLHelper::now( TRUE ) );
		return $this->save();
	}

	/**
	 * 
	 * @param type $confession_id
	 * @param type $approve
	 */
	public function approve($confession_id, $approve = TRUE)
	{
		$this->add_data( "seen", $approve ? 1 : -1  );
		$this->db->where( "id", $confession_id );
		return $this->update();
	}

	public function remove($confession_id)
	{
		//$result = $this->get_by_id($confession_id);
		//if (!$result)
		//	return false;

		$this->add_data( "seen", -2 );
		$this->db->where( "id", $confession_id );

		return $this->update();
		//$this->db->where("id",$confession_id)->delete($this->table_info->get_table_name());
		//$hm = new HashtagsModel;
		//$hm->delete_safe($result->hashtag_id);
	}

	public function count($deleted = false)
	{
		$this->db->where( "seen", !$deleted ? 1 : -1  );
		return $this->count_rows();
	}

	public function get_by_hashtag($hashtag)
	{
		$this->db->where( "LOWER(value)", strtolower( $hashtag ) )
				  ->where( "seen !=", -2 )
				  ->order_by( "created DESC" );

		return $this->_join_hashtags()->get();
	}

	public function filter($column, $value)
	{
		$this->db->where( $column, $value );
		return $this;
	}

}

?>

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
class HashtagsModel extends DML
{

	public function __construct()
	{

		parent::__construct( 'hashtags' );
	}

	public function remove($id)
	{
		
	}

	public function rename($hashtag_old, $hashtag_new)
	{
		$dont_delete = false;

		//= Pokud je novej hashtag null, smazeme starej a nic se nedeje
		if ( $hashtag_new != null )
		{
			//= Zjistime, jestli v db jiz tento tag je
			$this->db->where( "LOWER(value)", strtolower( $hashtag_new ) );
			if ( ($result = $this->get_one()) !== false )
				$id = $result->id;
			else
			{
				//= Novy hashtag jeste neni v db, je potreba ho zadat
				$this->add_data( "value", $hashtag_new );
				$this->save();
				$id = $this->last_id();
			}
		}
		else
		{
			//= Novej je nulovej, mrkneme se na id stareho a ten zkusime smazat.
			//= pote vratime null
			$this->db->where( "LOWER(value)", strtolower( $hashtag_old ) );
			$result = $this->get_one();
			if ( $result != false )
				$this->delete_safe( $result->id );
			return DML::NULL_VALUE;
		}

		if ( $hashtag_old == null )
		//= Pokud je puvodni hash nulovej, tak se pridal akorat novej
		//= a na starej se pece.
			return $id;
		else
		{
			$this->db->where( "LOWER(value)", strtolower( $hashtag_old ) );
			if ( ($result_old = $this->get_one()) !== false )
			{
				//= Tady by to melo byt vzdycky!!
				$id = $result_old->id;
			}
		}



		return $id;
	}

	public function delete_safe($hashtag_id)
	{
		if ( $this->count_hashtag_uses( $hashtag_id ) == 0 )
		{
			$this->db->where( "id", $hashtag_id )
					  ->delete( $this->table_info->get_table_name() );
			return true;
		}

		return false;
	}

	public function count_hashtag_uses($hashtag_id)
	{
		$this->table_info->change_table_name( "confessions" );
		$this->db->where( "hashtag_id", $hashtag_id );
		$count = $this->count_rows();
		$this->table_info->change_table_name( "hashtags" );
		return $count;
	}

	public function count_hashtags_uses()
	{
		$this->db->where( "seen", 1 )
				  ->where( "hashtag_id > ", 0 )
				  ->join( "confessions", "hashtags.id = confessions.hashtag_id", "right" )
				  ->select( "value as value_url, hashtag_id,count(*) as count" )
				  ->group_by( "hashtag_id" )
				  ->order_by( "count DESC" );
		return parent::get();
	}

	public function get()
	{
		$this->db->select( "CONCAT('#',value) as value", FALSE );

		$this->table_info->change_table_name( "confessions" );
		$this->db->join( "hashtags", "hashtags.id = confessions.hashtag_id", "left" )
				  ->where( "seen", 1 )
				  ->where( "hashtag_id >", 0 )
				  ->group_by( "hashtag_id" );
		$result = parent::get();
		$this->table_info->change_table_name( "hashtags" );

		return $result;
	}

	static function prepare_hashtag($hashtag)
	{
		$hashtag = trim( $hashtag, '#' );
		return trim( $hashtag, ' ' );
	}

	static function hashtag2url($hashtag)
	{
		return urlencode( self::prepare_hashtag( $hashtag ) );
	}

	public function add($hashtag)
	{

		$this->db->where( "value", $hashtag );

		$result = $this->get_one();

		if ( $result === FALSE )
		{

			$this->add_data( "value", $hashtag );
			$this->save();
			return $this->last_id();
		}
		else
		{
			return $result->id;
		}
	}

	public function count()
	{
		$this->table_info->change_table_name( "confessions" );
		$this->db->where( "seen", 1 )
				  ->where( "hashtag_id >", 0 );
		$count = $this->count_rows();
		$this->table_info->change_table_name( "hashtags" );
		return $count;
	}

	public function rename_over($old_id, $new_name)
	{
		$this->db->like( "value", $new_name );
		$result = $this->get_one();
		if ( $result == FALSE || $new_name !== $result->value )
		{
			//= Novy hashtag neexistuje, tak ho proste prejmenujeme a nazdar.
			$this->db->where( "id", $old_id );
			return $this->add_data( "value", $new_name )
								 ->update();
		}
		else
		{
			//= Vsechny priznani se starym hashtagem presuneme na novej
			$this->table_info->change_table_name( "confessions" );
			$this->db->where( "hashtag_id", $old_id );
			$this->add_data( "hashtag_id", $result->id )
					  ->update();
			$this->table_info->change_table_name( "hashtags" );

			//= starej hashtag smazeme
			$this->db->where( "id", $old_id )
					  ->delete( $this->table_info->get_table_name() );

			return true;
		}
	}

}

?>

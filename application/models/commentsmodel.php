<?php

/**
 * Description of AddresseModel
 * 
 * @author Pavel Vais
 */
class CommentsModel extends DML
{

	public function __construct()
	{
		parent::__construct( 'comments' );
	}

	public function get_comments($wish_id,$include_deleted = FALSE)
	{
		$this->db->where('wish_id',$wish_id);
		if (!$include_deleted)
			$this->db->where('deleted',0);
		$this->_join_user_info();
		
		$this->db->order_by("date_created","ASC");
		return $this->get();
	}
	
	public function get_comment($id)
	{
		$this->db->where($this->table_info->get_table_name().'.id',$id);
		//$this->_join_wisher_id();
		$this->_join_user_info();
				  
		return $this->get_one();	  
	}
	
	private function _join_wisher_id()
	{
		$this->db->join("wishes","wishes.id = ".$this->table_info->get_table_name().".wish_id");
		$this->db->select($this->table_info->get_table_name().".*, wishes.author_id AS wish_owner_id");
		return $this;
	}
	
	private function _join_user_info()
	{
		$this->db->join("users","users.id = ".$this->table_info->get_table_name().".user_id")
				  ->select($this->table_info->get_table_name().".*,users.username");
		return $this;
	}
		
	
	public function delete_comment($comment_id)
	{
		$this->db->where('id',$comment_id);
		//$this->db->deleted($this->table_info->get_table_name());
		$this->add_data("id",$comment_id)
				  ->add_data("deleted",1);
		return $this->save();
		
	}
	
	public function save_comment($user_id,$wish_id,$message)
	{
		$this->fetch_data(array(
			"user_id" => $user_id,
			 "wish_id" => $wish_id,
			 "message" => $message,
			 "date_created" => DMLHelper::now(TRUE)
		));
		
		return $this->save();
	}
}

<?php
namespace DML;
/**
 * Description of VariablesModel
 * @author Pavel Vais
 * 
 */
abstract class CommonModel extends Base
{
	
	/**
	 * Konstruktor tridy
	 */
	public function __construct($tableName)
	{
		parent::__construct( $tableName );
	}

	public function getByID($id)
	{
		$this->db->where('id',$id);
		return $this->dbGetOne();
	}
	
	public function modifyByID($id)
	{
		$this->db->where('id',$id);
		return $this->save();
	}
	
	/**
	 * Delete record by its ID
	 * @param int $id
	 * @return boolean
	 */
	public function deleteByID($id)
	{
		$this->db->where('id',$id);
		return $this->dbDelete();
	}
	
	

}
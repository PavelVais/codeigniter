<?php

/**
 * Description of VariablesModel
 * @author Pavel Vais
 * 
 */
class ErrorModel extends DML\CommonModel
{

	/**
	 * Konstruktor tridy
	 */
	public function __construct()
	{
		parent::__construct( 'errors' );
	}

	function getAll()
	{
		$this->db->order_by( 'date DESC' );
		return $this->dbGet();
	}

	function getNew()
	{
		$this->db->order_by( 'date DESC' )
				  ->where( 'viewed', 0 );
		return $this->dbGet();
	}
	

	function makeViewed()
	{
		$this->db->where( 'viewed', 0 );

		$this->addData( 'viewed', 1 );
		return $this->update();
	}
	
	function flush()
	{
		$this->db->where( 'viewed', 0 )
				  ->where('viewed',1);

		return $this->dbDelete();
	}

}

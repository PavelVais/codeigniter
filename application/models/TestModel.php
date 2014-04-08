<?php

namespace DML\Model;
/**
 * @author Pavel Vais
 */
class TestModel extends \DML\Base
{
	

	/**
	 * Konstruktor tridy,
	 */
	public function __construct()
	{
		//= Nastaveni tabulky users
		
		parent::__construct( 'books' );
		//DMLBuilder::removeDBCache();
	}
	
	public function g()
	{
		$this->dbJoin('users');
		
		$result = $this->get();
		
	}
	public function sav()
	{
		$this->change_table('users');
		$array = array(
				 'adresses_id' => 3,
				 'phone' => 4564414123,
				 'name' => 'Ondřej Kovodrtič'
			);
		$this->fetchData($array);
		$this->save();
		$this->change_table('books');
	}

	
	public function get_all()
	{
			$this->dbJoin('users','author_id')->left();
			$this->dbJoin('users','owner_id')->select(array('adresses_id','name'))->left();
			$this->dbJoin('adresses','owner_adresses_id','users')->left();
			$this->dbJoin('adresses','author_adresses_id','users')->left();
			
			$array = array(
				 'adresses_id' => 3,
				 'phone' => 48858665,
				 'name' => 'Marek Kovodrtič'
			);
					  
			$this->fetchData($array);
			
			$this->dbJoinMN('tags');
			
			//$this->save();
			
			dump($this->dbGet());
			dump($this->get_queries_history());
			dump($this->db->last_query());
			return ;
			
			
			/**
			 * BOOKS -> ma INCOMING v BOOKS_LIST -> ma OUT na author_id 
			 */
	}
	
	public function t()
	{
		$this->test();
	}
}

?>

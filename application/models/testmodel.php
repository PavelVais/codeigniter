<?php


/**
 * @author Pavel Vais
 */
class TestModel extends DML
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

	
	public function get_all()
	{
		
		//dump($this->tableInfo);
		//dump(DMLBuilder::loadTableInfo('books_tags'));
		/*
		 * Chci ziskat vsechny autory a k nim priradit vsechny knihy
		 * [0] -> 
		 *			....
		 *			books ->
		 *						.... 
		 */
		
		/**
		 * Ja vim, ze autor saha na books_list,
		 * musi se mrknout na books_list cache a tam najit, jestli neni
		 * propojena s books. Pkud ano, musi se z toho vygenerovat join
		 * 
		 * 1. Najit vsechny klice kteri smeruji na tuto tabulku
		 * 2. Najit sloupec s books_id
		 * 3. Najit kam ON odkazuje
		 *		3.1 - prohledat books_list cache
		 * 4. vygenerovat join('books', 'books_list.books_id = books.id');
		 * 5. vygenerovat join('books_list', 'books_list.authors_id = books_list.id');
		 */
			/*$this->dbJoinMN('books','count(*)',FALSE,function(){
				$this->db->group_by('authors_id');
			});*/
		
			//$this->dbJoin('users','author_id')->left();
			//$this->dbJoin('users','owner_id')->select(array('adresses_id','name'))->left();
			//$this->dbJoin('adresses','owner_adresses_id','users')->left();
			//$this->dbJoin('adresses','author_adresses_id','users')->left();
			
			$array = array(
				 'adresses_id' => 3,
				 'phone' => 48858665,
				 'name' => 'Marek Kovodrtič'
			);
					  
			$this->fetchData($array);
			
			$this->dbJoinMN('tags');
			
			//$this->save();
			
			dump($this->dbGet());
			//dump($this->get_queries_history());
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

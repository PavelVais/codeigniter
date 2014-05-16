<?php

/**
 * Description of VariablesModel
 * @author Pavel Vais
 * 
 */
class KeywordsModel extends DML\Base
{

	/**
	 * Konstruktor tridy
	 */
	public function __construct()
	{
		parent::__construct( 'keywords' );
	}

	public function search($term)
	{
		$this->db->like( 'name', $term );

		$result = $this->dbGet();
		$this->change_table( 'keywords' );
		return $result;
	}

	public function saveArticleKeyword($articleID, $keywordID)
	{
		$this->change_table( 'articles_keywords' );
		if ( is_array( $keywordID ) )
		{
			return $this->saveArticleKeywords( $articleID, $keywordID );
		}

		$this->addData( 'article_id', $articleID )
				  ->addData( 'keyword_id', $keywordID );

		return $this->save();
	}

	/**
	 * Ulozi do MN tabulky seznam vsech klic. slov vstahujici se k danemu clanku
	 * @param type $articleID - ID clanku
	 * @param type $keywordIDs - IDcka klic. slov
	 * @return type
	 */
	public function saveArticleKeywords($articleID, $keywordIDs)
	{
		$data = array();
		$this->change_table( 'articles_keywords' );
		foreach ( $keywordIDs as $row )
		{
			$data[] = array(
				 'article_id' => $articleID,
				 'keyword_id' => $row
			);
		}
		$this->addData( $data );

		return $this->save_batch();
	}

	/**
	 * Smaze klicove slovo z clanku
	 * @param type $articleID
	 * @param type $keywordID
	 * @return type
	 */
	public function deleteArticleKeyword($articleID, $keywordID)
	{
		$this->change_table( 'articles_keywords' );
		$this->db->where( 'article_id', $articleID )
				  ->where( 'keyword_id', $keywordID );

		return $this->dbDelete();
	}

	/**
	 * Smaze klicova slova z clanku
	 * @param int $articleID
	 * @return type
	 */
	public function deleteArticleKeywords($articleID)
	{
		$this->change_table( 'articles_keywords' );
		$this->db->where( 'article_id', $articleID );
		return $this->dbDelete();
	}

	/**
	 * Vyzkousi, jestli jsou vsechna vlozena slova v tabulce
	 * Pokud ano, vrati se FALSE, jinak TRUE
	 * @param array $names
	 * @return boolean
	 */
	public function isNew($names)
	{
		$this->change_table( 'keywords' );
		$this->db->where_in( 'name', $names );
		return $this->dbCountRows() == 0;
	}

	/**
	 * Ulozi do tabulky keywords novo klicove slovo
	 * Pokud klicove slovo je ARRAY, ulozi se jich vice (0 => array(id,name)...)
	 * @param String/Array $name
	 * @return
	 */
	public function save($name)
	{
		$this->change_table( 'keywords' );

		if ( is_array( $name ) )
		{
			\FB::info($name,'$name');
			$this->addData( $name );
			return parent::save_batch();
		}
		$this->addData( 'name', $name );
		return parent::save();
	}

	public function getKeywordsByArticle($articleID)
	{
		$this->change_table( 'articles_keywords' );
		$this->db->where( 'article_id', $articleID );
		return $this->dbGet();
	}

	public function getKeywordsIDS($names, $returnOnlyIDS = false)
	{
		$this->change_table( 'keywords' );
		$this->db->where_in( 'name', $names );
		$result = $this->dbGet();

		if ( !$result )
			return false;

		if ( $returnOnlyIDS )
		{
			$result = \DML\Helper::getValuesFromArrays( $result, 'id' );
		}
		return $result;
	}

	/**
	 * Hlavni funkce na ulozeni vsech klicovych slov, vstahujici se k clanku
	 * System nejdrive vse rozparsuje, vymaze predchozi klic. slova a 
	 * nahraje vsechny znovu. Tim nedojde k zadne duplikaci atp.
	 * @param int $articleID
	 * @param string $keywordsStream
	 * @return boolean
	 */
	public function saveArticle($articleID, $keywordsStream)
	{

		$this->deleteArticleKeywords( $articleID );

		if ( $keywordsStream == '' )
			return true;

		list($newKeywords, $oldKeywords) = $this->parseTagStream( $keywordsStream );
		
		//= Nejsou zadne nove klice k ulozeni? cajk, nic neresit.
		if ( !empty( $newKeywords ) )
		{
			$newKeywordsNames = \DML\Helper::getValuesFromArrays( $newKeywords, 'name' );

			// Doufam, ze novy klice nejsou ulozeny! -> chyba
			if ( !$this->isNew( $newKeywordsNames ) )
			{
				return false;
			}

			// Ulozime nova klic. slova a zjistime jejich ID
			$this->save( $newKeywords );
			$newKeysIDs = $this->getKeywordsIDS( $newKeywordsNames, TRUE );
			if ( count( $newKeysIDs ) != count( $newKeywords ) )
			{
				$this->sendDebugMessage(count( $newKeysIDs ).' != '.count( $newKeywords ), 'error při porovnání uložených klíč. slov');
				return false;
			}
		}
		else
		{
			$newKeysIDs = array();
		}

		// Sjednotime ID starych s ID novych klic. slov
		$idsToInsert = $this->mergeIDS( $newKeysIDs, \DML\Helper::getValuesFromArrays( $oldKeywords, 'id' ) );
		$this->sendDebugMessage( $idsToInsert, 'ID k ulozeni klic. slov' );
		return $this->saveArticleKeywords( $articleID, $idsToInsert );
	}

	/**
	 * Rozparsuje string (0:novyTag,0:dalsiNovyTag,1:hokej,5:dalsiTag)
	 * na pole novych tagu (s nulou jako ID) a na pole s tagama,
	 * ktere cekaji na prirazeni
	 * @param type $dataStream
	 * @return array(tagy k ulozeni,tagy k prirazeni
	 */
	public function parseTagStream($dataStream)
	{
		// Vse s ID 0 chceme ulozit
		$data = explode( ',', $dataStream );
		$newData = array();
		$assignData = array();
		foreach ( $data as $tag )
		{
			$rowData = explode( ':', $tag );
			if ( (int) $rowData[0] == 0 )
			{
				$newData[] = array(
					 'name' => (string) $rowData[1]
				);
			}
			else
			{
				$assignData[] = array(
					 'id' => (int) $rowData[0],
					 'name' => (string) $rowData[1]
				);
			}
		}

		return array($newData, $assignData);
	}

	public function mergeIDS($newIDs, $oldIDs)
	{
		return array_unique( array_merge( $newIDs, $oldIDs ) );
	}

	public function addCountToQuery()
	{
		$this->change_table( 'articles_keywords' );
		$this->dbJoin( 'keywords' )
				  ->right()
				  ->select( '!COUNT(articles_keywords.keyword_id) as count,name,id' );
		$this->db->group_by( $this->name . '.keyword_id' )->order_by( 'count DESC' );
		return $this;
	}

}

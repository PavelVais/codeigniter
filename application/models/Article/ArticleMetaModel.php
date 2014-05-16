<?php

namespace Model\Article;

//require_once('PasswordHash.php');

/**
 * Description of ArticleMetaModel
 * 
 * @author Pavel Vais
 */
class ArticleMetaModel extends \DML\Base
{

	private $metas = array();
	private $allowedMetas = array();

	public function __construct()
	{
		parent::__construct( 'article_meta' );
		$this->allowedMetas = array('og:title', 'og:description', 'og:url', 'og:image', 'og:type', 'og:site_name', 'og:locale');
	}

	public function getArticleMetas($articleID, $metasNames = null)
	{
		if ( $metasNames != null )
		{
			if ( is_array( $metasNames ) )
				$this->db->where_in( 'name', $metasNames );
			else
				$this->db->where( 'name', $metasNames );
		}
		$this->db->where( 'article_id', $articleID );
	}

	public function assignMetasToArticle($articleID, $deletePrevious = false)
	{
		if ( $deletePrevious )
			$this->deleteArticleMetas( $articleID );

		return $this->save_batch();
	}

	/**
	 * Pripravi meta tag pro ulozeni
	 * @param string $name
	 * @param string $value
	 */
	public function addMetaForSave($name, $value)
	{
		$name = $this->filterAllowedMetas( $name );
		if ( !$name )
			return $this;

		$this->addData( array(
			 'name' => $name,
			 'value' => $value
		) );
		return $this;
	}

	public function deleteArticleMetas($articleID, $metas = null)
	{
		if ( !is_array( $metas ) && $metas !== null )
			$metas = array($metas);

		if ( $metas !== null )
		{
			$this->db->where_in( 'name', $metas );
		}

		$this->db->where( 'article_id', $articleID );
		return $this->dbDelete();
	}

	/**
	 * Vsechny meta tagy, ktere se nesmi ulozit do db (ktere nejsou povoleny)
	 * se vymazou. Tato funkce se musi zavolat jakmile se ukladaji do db
	 * @param string/array $metas
	 * @return array/false
	 */
	public function filterAllowedMetas($metas)
	{
		$notArray = false;
		if ( !is_array( $metas ) )
		{
			$metas = array($metas);
			$notArray = true;
		}

		foreach ( $metas as $key => $meta )
		{
			if ( !in_array( $meta, $this->allowedMetas ) )
			{
				if ( $notArray )
					return false;
				unset( $metas[$key] );
			}
		}

		return $notArray && isset( $metas[0] ) ? $metas[0] : ($notArray ? false : $metas);
	}

	public function get_news_meta($news_id, $meta_name)
	{
		$this->table_info->change_table_name( "news_meta" );
		$this->db->where( "news_id", $news_id )
				  ->where( 'name', $meta_name )
				  ->select( "value", $meta_name );

		$result = $this->get_one();

		return $result !== FALSE ? $result->value : FALSE;
	}

	public function get_all_news_metas($news_id)
	{
		$result = $this->_get_news_meta_active_row( $news_id );

		if ( $result === FALSE )
			return FALSE;

		$return = array();
		foreach ( $result as $row )
		{
			$return[$row->name] = $row->value;
		}

		return $return;
	}

	public function add_news_meta($name, $value)
	{
		$this->metas[] = array(
			 "name" => $name,
			 "value" => $value
		);
		return $this;
	}

	private function _get_news_meta_active_row($news_id)
	{
		$this->table_info->change_table_name( "news_meta" );
		$this->db->where( "news_id", $news_id );
		return $this->get();
	}

	public function delete_news_meta($news_id, $meta_name)
	{
		$this->table_info->change_table_name( "news_meta" );
		$this->db->where( "name", $meta_name );
		$this->db->delete( $this->table_info->get_table_name() );
		$this->log_operation();
		return true;
	}

	public function save_news_meta($news_id)
	{

		$this->table_info->change_table_name( "news_meta" );
		$metas = $this->_get_news_meta_active_row( $news_id );
		$update = array();
		if ( $metas !== FALSE )
		{
			foreach ( $metas as $meta )
			{

				foreach ( $this->metas as $key => $inc_meta )
				{
					if ( $inc_meta['name'] == $meta->name )
					{
						$update[] = array(
							 'id' => $meta->id,
							 'news_id' => $news_id,
							 'name' => $inc_meta['name'],
							 'value' => $inc_meta['value']
						);
						unset( $this->metas[$key] );
					}
				}
			}
		}

		//= Pridani IDcka zpravy pro metu
		foreach ( $this->metas as &$inc_meta )
		{
			$inc_meta['news_id'] = $news_id;
		}


		//= Mame roztrideny promenny
		//= v $updatu jsou ty, ktery uz v databazi existuji (UPDATE)
		//= a v $this->metas zustaly ty promenne, ktere potrebuji vlozit (INSERT)
		if ( count( $this->metas ) > 0 )
		{
			$this->fetch_data( $this->metas );
			$result = $this->save_batch();
		}

		if ( count( $update ) > 0 )
		{
			$this->fetch_data( $update );
			return $this->save_batch();
		}

		return $result;
	}

	public function update_news_value($news_id, $request, $value)
	{
		$this->db->where( "id", $news_id );
		return $this->add_data( $request, $value )
							 ->add_data( "id", $news_id )
							 ->save();
	}

}

<?php
namespace Model\Article;
//require_once('PasswordHash.php');

/**
 * Description of ConfirmModel
 * Tento model zajistuje generovani a nasledne checkovani potvrzovacich
 * retezcu.
 * Vhodne pro linky, ktere jsou pristupne jen docasne pro toho, kdo zna dany
 * hash
 * @author Pavel Vais
 */
class ArticleModel extends \DML\Base
{

	private $meta_table = "news_meta";
	private $metas = array();

	public function __construct()
	{
		parent::__construct( 'articles' );
	}

	public function save_news()
	{

		return $this->save();
	}

	public function get_news($id)
	{
		$this->db->where( 'id', $id );

		$result = $this->dbGetOne();

		if ( $result !== false )
		{
			$m = $this->get_all_news_metas( $id );
			$result->meta = ($m == FALSE ? null : $m);
		}

		return $result;
	}

	public function get_all_news($page, $num_rows)
	{
		$this->_join_author_name();
		return $this->dbGet();
	}

	public function is_news_exists($id)
	{
		$this->db->select( "id" )
				  ->where( "id", $id );
		return ($this->get_one() === FALSE) ? FALSE : TRUE;
	}

	public function hide_news($hide = true)
	{
		
	}

	private function _join_author_name()
	{
		$this->db->join( "users", "users.id = " . $this->name . ".author_id" )
				  ->select( $this->name . ".*,users.username" );

		return $this;
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
		$this->change_table("article_meta" );
		$this->db->where( "article_id", $news_id );
		return $this->dbGet();
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
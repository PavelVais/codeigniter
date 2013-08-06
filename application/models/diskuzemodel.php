<?php
/**
 * Description of dummyModel
 *
 * @author Daw
 */
class DiskuzeModel extends DML
{

	private $result;
	private $indent = 0;
	private $ids_for_delete;

	const ROWS_PER_PAGE = 4;

	public function __construct()
	{

		parent::__construct( 'diskuze' );
		
	}

	public function getDiskuzi($strana = 1)
	{
		$strana = $strana - 1;
		if ( $strana == 0 )
		{
			$cache = new DMLCache();
			$cache->add_tag( 'all' );
			//$this->query_cache_activation( $cache );
		}
		$this->db->where( 'answer_id', 0 )->order_by( 'date_created DESC' );
		$this->db->limit( self::ROWS_PER_PAGE, self::ROWS_PER_PAGE * ($strana) );

		$result = $this->get();
		$this->result = array();
		$this->indent = 0;

		if ( $result == FALSE )
		{
			return FALSE;
		}

		if ( !is_array( $result ) )
			$result = array($result);

		foreach ( $result as $prispevek )
		{
			$prispevek->indent = 0;
			$this->result[] = $prispevek;
			$this->_fetch_comments( $prispevek->id );
		}

		return $this->result;
	}

	/**
	 * Smaze danou spravu a jeji subkomentare
	 * @param int $id 
	 */
	public function deleteComment($id)
	{
		
		$this->ids_for_delete = array();
		$this->_delete_SubComments($id);
		$this->ids_for_delete[] = $id;
		$this->db->where_in( 'id', $this->ids_for_delete );
		$this->db->delete($this->table_info->name);
	}

	/**
	 * Zjisti vsechny idcka komentaru, ktere se vstahuji k rodici
	 * @param type $id
	 * @return type 
	 */
	private function _delete_SubComments($id)
	{
		$prispevky = $this->getSubComments( $id );

		if ( $prispevky == false )
			return;

		if ( !is_array( $prispevky ) )
			$prispevky = array($prispevky);

		foreach ( $prispevky as $prispevek )
		{
			$this->_delete_SubComments( $prispevek->id );
			$this->ids_for_delete[] = $prispevek->id;
		}
	}
	
	/**
	 * Zjisti vsechny subkomenty k danemu komentari
	 * @param int $id
	 * @return type 
	 */
	public function getSubComments($id)
	{
		$this->db->where( 'answer_id', $id );
		return $this->get();
	}

	/**
	 * Zmeni zneni komentare
	 * @param int $id - povinny udaj
	 * @param String $nickname - pokud chcete zmenit nick, tak ho uvedte
	 * @param String $text - pokud chcete zmenit zpravu, tak ji uvedte
	 * @return type 
	 */
	public function editComment($id, $nickname = null, $text = null,$contact = null)
	{
		$this->db->where( 'id', $id );

		if ( $nickname != null )
			$this->add_data( 'nickname', $nickname );

		if ( $text != null )
			$this->add_data( 'content', $text );
		
		if ( $contact != null )
			$this->add_data( 'contact', $contact );

		return $this->update();
	}

	/**
	 * Ziska komentar pod danym id
	 * @param int $id
	 * @return type 
	 */
	public function getComment($id)
	{
		$this->db->where( 'id', $id );
		return $this->get_one();
	}

	private function _fetch_comments($id)
	{

		$prispevky = $this->getSubComments( $id );

		if ( $prispevky == false )
			return;

		if ( !is_array( $prispevky ) )
			$prispevky = array($prispevky);

		foreach ( $prispevky as $prispevek )
		{
			$this->indent++;
			$prispevek->indent = $this->indent;

			$this->result[] = $prispevek;
			$this->_fetch_comments( $prispevek->id );
			$this->indent--;
		}
	}

	/**
	 * Zjisti, jak velka je diskuze
	 * @return int 
	 */
	public function getDiskuzeSize()
	{
		$this->db->where( 'answer_id', 0 );
		return $this->count_rows();
	}

	/**
	 * Ulozi komentar
	 * @return type 
	 */
	public function saveComment()
	{
		$this->add_data( array(
			 'date_created' => DMLHelper::now( TRUE )
		) );

		return $this->save();
	}

}

?>

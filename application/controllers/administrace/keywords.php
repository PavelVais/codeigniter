<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of keywords
 *
 * @author Daw
 */
class keywords extends My_Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Ziska vsechny kategorie serazene dle pouzivani
	 * @param String
	 */
	public function get($term = null)
	{
		
		$term = $term || $this->input->post( 'q' );

		$keyModel = new Model\KeywordsModel;
		$result = $keyModel->addCountToQuery()->search( $term );

		if ( !$result )
			$this->output->json_append( 'status', 404 )
					  ->json_flush();

		else
		{
			$result2 = array();
			foreach ( $result as $row )
			{
				$result2[] = array(
					 'id' => $row->keyword_id,
					 'text' => $row->keyword_name,
					 'count' => $row->count
				);
			}
			$this->output->json_append( 'response', $result2 )
					  ->json_flush();
		}
	}
	
	

}

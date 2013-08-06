<?php

/**
 * Description of AddresseModel
 * 
 * @author Pavel Vais
 */
class AddresseeModel extends DML
{

	public function __construct()
	{

		parent::__construct( 'addressee' );
	}

	/**
	 * @todo Pripravit moznost vlozit fcb-id
	 * @param type $wish_id
	 * @param String $email
	 * @param type $fcb_id
	 * @return boolean
	 */
	public function add_addressee($wish_id, $email, $fcb_id = 0)
	{
		//if (!is_array($emails))
		//	$emails = array($emails);
		//$ids = array();
		//$ids = array_merge($ids, $this->get_id_from_already_saved_emails($emails));
		$this->table_info->change_table_name( "addressee" );
		$this->db->where( "email", $email );
		$this->db->select( "id" );
		$result = $this->get_one();
		if ( $result != FALSE )
		{
			//= Email uz existuje, neni treba davat dalsi
			$id = $result->id;
		}
		else
		{
			//= Jinak se vlozi novy email a zjisti se jeho IDcko
			$this->add_data( "email", $email );

			if ( $this->save() != FALSE )
			{
				$id = $this->db->insert_id();
			}
			else
			{
				return FALSE;
			}
		}

		//= Zmena tabulky na M:M -> vlozit potrebna ID
		$this->table_info->change_table_name( "addressees" );

		$this->add_data( "addressee_id", $id );
		$this->add_data( "wish_id", $wish_id );
		if ( $this->save() == FALSE )
		{
			//TODO: smazat email z db
			dump( $this->get_error_message() );

			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Vrati vsechny emaily pridruzene k danemu prani
	 * (emaily, pro ktere prani je urcene)
	 * @param type $whish_id
	 * @return type
	 */
	public function get_addresses($whish_id)
	{
		$this->db->join( 'addressees', $this->table_info->get_table_name() . '.id = addressees.id' );
		$this->table_info->get_table_name = "addressees";

		if ( $result = $this->get() )
		{
			$return = array();
			foreach ( $result as $email )
			{
				$return[] = $email->email;
			}
			return $return;
		}
		return null;
	}

	public function get_id_from_already_saved_emails(&$emails)
	{
		//= Podivat se, kolik z nich existuje
		$this->db->where_in( "email", $emails );
		$this->db->select( "id" );

		$ids = array();
		//= Musime model preorientovat na email tabulku
		if ( $result = $this->get() )
		{
			//= Musime vyradit existujici z insertu
			foreach ( $emails as $key => $email )
			{
				foreach ( $result as $result_email )
				{
					$ids[] = $result_email->id;
					if ( $email == $result_email->email )
					{
						unset( $emails[$key] );
						break;
					}
				}
			}
		}

		return $ids;
	}

}

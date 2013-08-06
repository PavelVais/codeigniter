<?php

/**
 * Description of AddresseModel
 * 
 * @author Pavel Vais
 */
class SchemesModel extends DML
{

	public function __construct()
	{
		parent::__construct( 'schemes' );
	}

	public function add_scheme($name)
	{
		$this->add_data("name", $name);
		return $this->save();
		
	}

}

<?php

/**
 * Description of FormElement
 *
 * @author Daw
 */
class Form_password extends Form_input
{

	public function __construct($value, $name, $maxLength, $size, $label = null, $id = null )
	{
		parent::__construct($value, $name, $maxLength, $size, $label) ;
	}

	public function generateCode()
	{
		$data = array(
			 'name' => parent::getName(),
			 'id' => parent::getId(),
			 'value' => parent::getValue(),
			 'maxlength' => $this->getMaxLength(),
			 'size' => $this->getSize()
		);

		if ( parent::getExtra() != null )
		{
			$data[] = parent::getExtra();
		}

		parent::generateCode( form_password( $data ) );
	}
}


<?php

/**
 * Description of FormElement
 *
 * @author Daw
 */
class Form_input extends FormElement
{

	private $maxLength;
	private $size;

	public function __construct($value, $name, $maxLength, $size, $label = null, $id = null)
	{
		parent::__construct( $value, $name, $id, $label );
		$this->maxLength = $maxLength;
		$this->size = $size;
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

		parent::generateCode( form_input( $data ) );
	}

	public function getMaxLength()
	{
		return $this->maxLength;
	}

	public function setMaxLength($maxLength)
	{
		$this->maxLength = $maxLength;
	}

	public function getSize()
	{
		return $this->size;
	}

	public function setSize($size)
	{
		$this->size = $size;
	}

}


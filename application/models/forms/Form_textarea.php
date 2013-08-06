<?php

/**
 * Description of FormElement
 *
 * @author Daw
 */
class Form_textarea extends FormElement
{

	private $rows;
	private $cols;

	public function __construct($value, $name, $rows, $cols, $id = null, $label = null)
	{
		parent::__construct( $value, $name, $id, $label );
		$this->rows = $rows;
		$this->cols = $cols;
	}

	public function generateCode()
	{
		$data = array(
			 'name' => parent::getName(),
			 'id' => parent::getId(),
			 'value' => parent::getValue(),
			 'rows' => $this->getRows(),
			 'cols' => $this->getCols()
		);

		if ( parent::getExtra() != null )
		{
			$data[] = parent::getExtra();
		}

		parent::generateCode( form_textarea( $data ) );
	}

	public function getRows()
	{
		return $this->rows;
	}

	public function getCols()
	{
		return $this->cols;
	}

}


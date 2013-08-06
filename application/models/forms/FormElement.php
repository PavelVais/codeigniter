<?php

/**
 * Description of FormElement
 *
 * @author Daw
 */
class FormElement
{

	private $value;
	private $id;
	private $name;
	private $extra;
	private $labelName;

	public function __construct($value, $name, $id = null, $labelName = null)
	{
		
		$this->value = $value;
		$this->labelName = $labelName;
		$this->id = is_null( $id ) ? $name : $id;
	}

	protected function generateCode($string)
	{
		echo $string;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getExtra()
	{
		return $this->extra;
	}

	public function getLabelName()
	{
		return $this->labelName;
	}

}


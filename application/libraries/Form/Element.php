<?php

namespace Form;

class Element
{

	public $name;

	public function __construct($name)
	{
		$this->name = $name;
	}

	public function __toString()
	{
		return $this->name;
	}

	public function setLabel($label)
	{
		$this->label = $label;
	}

}

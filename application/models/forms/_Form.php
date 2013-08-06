<?php

class Form
{

	private $elements = array();
	private $printLabels = TRUE;

	public function __construct($printLabels = TRUE)
	{
		$this->printLabels = $printLabels;
	}

	public function addElement(FormElement $element)
	{
		$this->elements[] = $element;
		return $this;
	}

	public function generate()
	{
		foreach ( $this->elements AS $element )
		{
			if ( $this->printLabels == TRUE )
			{
				echo form_label( $element->getLabelName(), $element->getId() ) . "\n";
			}
			echo $element->generateCode() . "\n";
		}
	}

	public function printLabels($boolean = TRUE)
	{
		$this->printLabels = $boolean;
	}

}

?>

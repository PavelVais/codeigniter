<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class HTMLElement
{

	static function open($element_name)
	{
		$element = new HTMLconstructor( $element_name );
		return $element;
	}

}

class HTMLconstructor
{

	private $elementName;
	private $elementAttr = array();
	private $appended = array();
	private $chained = array();
	private $is_pair = true;
	static private $indent = 0;

	public function __construct($elementName)
	{
		$this->elementName = $elementName;
	}

	public function isPair($enable = true)
	{
		$this->is_pair = $enable;
		return $this;
	}

	public function setFirstIndent($numberOfTabs)
	{
		self::$indent = $numberOfTabs;
		return $this;
	}

	public function addAttribute($name, $value = null)
	{
		$this->elementAttr[$name] = $value == null ? false : $value;
		return $this;
	}

	/**
	 * Vnori do prvku jiny prvek. Muze se zde predat cela trida HTMLconstructor
	 * nebo promenna. Jelikoz je zde reference, NESMI zde byt obycejny string.
	 * K tomu slouzi funkce appendString()
	 * @param mixed $mixed
	 * @return \HTMLconstructor
	 */
	public function append(&$mixed, $withoutReference = false)
	{
		if ( $withoutReference )
			$this->appended[] = $mixed;
		else
			$this->appended[] = &$mixed;
		
		return $this;
	}

	/**
	 * Pripoji k prvku dalsi prvek a zaradi ho hned za neho.
	 * @param HTMLconstructor $element
	 * @return \HTMLconstructor
	 */
	public function next(HTMLconstructor &$element, $withoutReference = false)
	{
		if ( $withoutReference )
			$this->chained[] = $element;
		else
			$this->chained[] = &$element;
		return $this;
	}

	/**
	 * Do prvku vlozi obycejny string. Nic jineho zde neni
	 * doporuceno vkladat.
	 * @param type $string
	 * @return \HTMLconstructor
	 */
	public function appendString($string)
	{
		$this->appended[] = $string;
		return $this;
	}

	public function generate()
	{
		//= Nejdrive odsadime element
		$string = $this->formatText();

		//= Nejdrive otevreme element
		$string .= '<' . $this->elementName;

		//= Pridame vsechny attributy
		foreach ( $this->elementAttr as $name => $attr )
		{
			$string .= $this->printAttribute( $name, $attr );
		}

		//= Na konci uzavreme prvni element
		$string .= '>';

		//= Zarovnani kodu
		HTMLconstructor::$indent++;

		//= Je neco appendovaneho?
		foreach ( $this->appended as $append )
		{
			if ( $append instanceof HTMLconstructor )
				$string .= $append->generate();
			else
				$string .= $this->formatText( $append );
		}

		HTMLconstructor::$indent--;

		//= A ted uz jen uzavrit
		if ( $this->is_pair )
		{
			$string .= $this->formatText( '</' . $this->elementName . '>' );
		}

		//= Je zde neco k vygenerovani jako dalsi element?
		foreach ( $this->chained as $chain )
		{
			$string .= $chain->generate();
		}

		return $string;
	}

	private function printAttribute($name, $value = false)
	{
		return ' ' . $name . ($value != false ? '="' . $value . '"' : '');
	}

	private function formatText($string = '')
	{
		return PHP_EOL . str_repeat( "\t", self::$indent ) . $string;
	}

}
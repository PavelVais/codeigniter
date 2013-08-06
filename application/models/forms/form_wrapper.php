<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Form_Wrapper
 *
 * @author Daw
 */
class Form_Wrapper
{

	private $control_wrapper;
	private $label_wrapper;
	private $element_wrapper;

	const CONTROL_WRAPPER = 'control';
	const LABEL_WRAPPER = 'label';
	const ELEMENT_WRAPPER = 'element';

	public function __construct()
	{
		
	}

	/**
	 * Zaobali jen prednim html prvkem (<prvek>text)
	 * @param String $text - text, ktery bude zaobalen
	 * @param type $wrapper - typ wrapperu
	 * @return String 
	 */
	public function startWrap($text, $wrapper = self::ELEMENT_WRAPPER)
	{
		return $this->wrap($text, $wrapper, FALSE, TRUE);
	}
	
	/**
	 * Zaobali jen zadnim html prvkem (text</prvek>)
	 * @param String $text - text, ktery bude zaobalen
	 * @param type $wrapper - typ wrapperu
	 * @return String 
	 */
	public function endWrap($text, $wrapper = self::ELEMENT_WRAPPER)
	{
		return $this->wrap($text, $wrapper, TRUE, FALSE);
	}
	
	/**
	 * Dle argumentu zaobali text do html prvku
	 * @param String $text
	 * @param String $wrapper
	 * @param boolean $end_pair - TRUE -> ukonci html prvek
	 * @param boolean $first_element - TRUE -> prida zacinajici html prvek
	 * @return String 
	 */
	public function wrap($text, $wrapper = self::ELEMENT_WRAPPER, $end_pair = TRUE, $first_element = TRUE)
	{
		switch ($wrapper)
		{
			case self::CONTROL_WRAPPER:
			default:
				$f_e = $this->getFirstElement( $this->control_wrapper );
				$f_l = $this->getLastElement( $this->control_wrapper );

				break;
			case self::LABEL_WRAPPER:
				$f_e = $this->getFirstElement( $this->label_wrapper );
				$f_l = $this->getLastElement( $this->label_wrapper );
				break;


			case self::ELEMENT_WRAPPER:
				$f_e = $this->getFirstElement( $this->element_wrapper );
				$f_l = $this->getLastElement( $this->element_wrapper );
				break;
		}

		return ($first_element ? $f_e : '') . $text . ($end_pair ? $f_l : '');
	}

	private function getFirstElement($text)
	{
		return "<$text>";
	}

	private function getLastElement($text)
	{
		return "</$text>";
	}

	/**
	 * nastavi html prvek pro Label form
	 * @param string $value 
	 */
	public function setLabelWrapper($value)
	{
		$this->label_wrapper = $value;
	}

	/**
	 * nastavi html prvek pro celou skupinu (Label a Element)
	 * @param string $value 
	 */
	public function setControlWrapper($value)
	{
		$this->control_wrapper = $value;
	}

	/**
	 * nastavi html prvek pro element (input / textarea...)
	 * @param string $value 
	 */
	public function setElementWrapper($value)
	{
		$this->element_wrapper = $value;
	}

}

?>

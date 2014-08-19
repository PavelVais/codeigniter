<?php
namespace HTML;
/**
 * @version 2.1
 *    2.1: Pridana funkce addData() a addClass() - zkratky pro addAttributes
 *		2.0: Pridan namespace HTML, HTMLConstructor a HTMLElement byly sjednoceny do HTML\Element
 *			- Vsechyn funkce jsou lepe zdokumentovany
 * 	1.1:
 * 		- Pokud se ve value objevi ", pote se spravne vypise element
 * 		- do prikazu addAttribute je mozne vlozit array a objekt.
 * 		  Ten je nasledne preveden do JSON formatu
 * 			
 */
class Element
{
	private $elementName;
	private $elementAttr = array();
	private $appended = array();
	private $chained = array();
	private $is_pair = true;
	static private $indent = 0;
	
	/**
	 * Singleton pro vytvoreni instance Element
	 * @param string $element_name - nazev html prvku (div,p, aj.)
	 * @return \HTML\Element
	 */
	static function open($element_name)
	{
		$element = new Element( $element_name );
		return $element;
	}

	public function __construct($elementName)
	{
		$this->elementName = $elementName;
	}

	/**
	 * Je tag parovy? (default: ano)
	 * @param boolean $enable
	 * @return \HTML\Element
	 */
	public function isPair($enable = true)
	{
		$this->is_pair = $enable;
		return $this;
	}

	/**
	 * Nastaveni odsazeni prvku
	 * @param int $numberOfTabs
	 * @return \HTML\Element
	 */
	public function setFirstIndent($numberOfTabs)
	{
		self::$indent = $numberOfTabs;
		return $this;
	}

	/**
	 * Prida do elementu atribut
	 * @param string $name
	 * @param string $value [optional]
	 * - muze byt string nebo objekt a array. Pokud se vklada objekt nebo array
	 * automaticky je preveden na JSON format (predpoklada se, ze se vklada
	 * do atributu 'data-'
	 * @return \HTML\Element
	 */
	public function addAttribute($name, $value = null)
	{
		if ( is_object( $value ) || is_array( $value ) )
		{
			$value = json_encode( (array) $value );
		}

		$this->elementAttr[$name] = $value == null ? false : $value;
		return $this;
	}
	
	/**
	 * Shortcut pro addAttribute(data-...)
	 * @param type $name
	 * @param type $value
	 * @return \HTML\Element
	 */
	public function addData($name,$value)
	{
		$this->addAttribute('data-'.$name, $value);
		return $this;
	}
	
	/**
	 * Shortcut pro addAttribute(class,...)
	 * @param string $value
	 * @return \HTML\Element
	 */
	public function addClass($value)
	{
		$this->addAttribute('class', $value);
		return $this;
	}

	/**
	 * Vlozi do atributu dalsi kus stringu. Oddeli je mezerou.
	 * Vhodne napr. pri vkladani vice classu. Oproti addAttribute nepremazava
	 * hodnotu
	 * @param string $name
	 * @param string $value
	 * @return \HTML\Element
	 */
	public function appendToAttribute($name, $value)
	{
		if ( isset( $this->elementAttr[$name] ) )
		{
			$this->elementAttr[$name] = $this->elementAttr[$name] . ' ' . $value;
		}
		else
			$this->addAttribute( $name, $value );

		return $this;
	}

	/**
	 * Vnori do prvku jiny prvek. Muze se zde predat cela trida HTMLconstructor
	 * nebo promenna. Jelikoz je zde reference, NESMI zde byt obycejny string.
	 * K tomu slouzi funkce appendString()
	 * @param mixed $mixed
	 * @return \HTML\Element
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
	 * @param \HTML\Element $element
	 * @return \HTML\Element
	 */
	public function next(\HTML\Element &$element, $withoutReference = false)
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
	 * @return \HTML\Element
	 */
	public function appendString($string)
	{
		$this->appended[] = $string;
		return $this;
	}

	/**
	 * Vrati vygenerovany HTML kod
	 * @return string
	 */
	public function generate()
	{
		//= Nejdrive odsadime element
		$string = $this->formatText();

		//= Nejdrive otevreme element
		$string .= $this->elementName !== '' ? '<' . $this->elementName : '';

		//= Pridame vsechny attributy
		foreach ( $this->elementAttr as $name => $attr )
		{
			$string .= $this->printAttribute( $name, $attr );
		}

		//= Na konci uzavreme prvni element
		$string .= $this->elementName !== '' ? '>' : '';

		//= Zarovnani kodu
		Element::$indent++;

		//= Je neco appendovaneho?
		foreach ( $this->appended as $append )
		{
			if ( $append instanceof Element )
				$string .= $append->generate();
			else
				$string .= $this->formatText( $append );
		}

		Element::$indent--;

		//= A ted uz jen uzavrit
		if ( $this->is_pair && $this->elementName !== ''  )
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
		if ( strpos( $value, '"' ) !== FALSE )
		{
			return ' ' . $name . '=\'' . $value . '\'';
		}
		else
		{
			return ' ' . $name . ($value != false ? '="' . $value . '"' : '');
		}
	}

	private function formatText($string = '')
	{
		return PHP_EOL . str_repeat( "\t", Element::$indent ) . $string;
	}

}

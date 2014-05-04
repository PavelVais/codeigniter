<?php

namespace Form;

/**
 * Trida Form slouzi k zlehceni generovani formularovych prvku.
 * Umoznuje i pridavat k prvkum validacni pravidla, ktere muze
 * javascript ("jquery.formvalidation.js") validovat.
 * @todo Ze tridy vyjmout validacni prvky a udelat z nich dalsi tridu
 * @author Pavel Vais
 * @version 1.2
 */
class Form
{

	/**
	 * @var Array
	 */
	private $elements = array();

	/**
	 * Odkazuje na posledni vlozeny element , popripade aktualne aktivni element
	 * Diky nemu neni za potrebi stale psat nazvy elementu pri upravach
	 * atributu, pridavani validatoru atp.
	 * @var Array 
	 */
	public $active_element;

	/**
	 * Wrapper slouzici k obalovani html prvku
	 * @var Form_Wrapper
	 */
	private $wrapper;

	/**
	 * Urcuje, jestli vsechny texty (errory, labely aj.)
	 * brat jako odkazy na jazykovy balicky nebo je brat jako
	 * normalni text
	 * @var type 
	 */
	private $use_lang_file = FALSE;

	/**
	 * Atributy pridruzene k form prvku
	 * @var Array 
	 */
	private $form_attributes = array();
	private $fieldset_opened = false;
	private $submit_url;

	/**
	 * Validacni pravidlo: prvek nesmi zustat prazdny
	 */
	const RULE_FILLED = 'filled';

	/**
	 * Validacni pravidlo: prvek musi byt email
	 */
	const RULE_EMAIL = 'email';

	/**
	 * Validacni pravidlo: vlozene znaky v elementu musi byt cisla
	 */
	const RULE_NUMBER = 'number';

	/**
	 * Validacni pravidlo: musi obsahovat presne X znaku
	 */
	const RULE_MIN_CHARS = 'min_chars';

	/**
	 * Validacni pravidlo: musi byt zaskrtnut (plati pro checkboxy)
	 */
	const RULE_CHECKED = 'checked';

	/**
	 * Validacni pravidlo: Musi byt validni tel. cislo
	 */
	const RULE_PHONE = 'phone';

	/**
	 * Validacni pravidlo: musi byt stejny jako X prvek
	 * (jako argument prijde nazev jineho form. prvku)
	 */
	const RULE_SAME_AS = 'same';

	/**
	 * microdata element, ktery rika validatoru, KAM se ma 
	 * validace vypsat
	 */
	const VALIDATION_RENDER_ELEMENT = "data-validationrender";

	/**
	 * Konstanta, ktera urcuje, jaka funkce se zavola s error. hlaskami
	 */
	const VALIDATION_ONERRROR_CALLBACK = "data-validationcallback";

	/**
	 * Hlavni konstruktor
	 *  
	 */
	public function __construct($destination_url = null)
	{
		$this->ci = & get_instance();
		$this->setDestination( $destination_url );
	}

	/**
	 * Vlozi do formu input
	 * @param String $name
	 * @param String $label
	 * @param int $size
	 * @param int $maxSize
	 * @return \Form
	 */
	public function addText($name, $label = null, $size = 20, $maxSize = 20)
	{
		$data = array(
			 'metadata' => array(
				  'type' => 'input',
				  'label' => $label
			 ),
			 'data' => array(
				  'name' => $name,
				  'id' => 'frm_' . $name,
				  'maxlength' => $maxSize,
				  'size' => $size
			 )
		);

		$this->add_element( $data );
		return $this;
	}

	/**
	 * Vlozi heslo do formulare
	 * @param type $name
	 * @param type $label
	 * @param type $size
	 * @param type $maxSize
	 * @return \Form\Form
	 */
	public function addPassword($name, $label = null, $size = 20, $maxSize = 20)
	{
		$data = array(
			 'metadata' => array(
				  'type' => 'password',
				  'label' => $label
			 ),
			 'data' => array(
				  'name' => $name,
				  'id' => 'frm_' . $name,
				  'maxlength' => $maxSize,
				  'size' => $size
			 )
		);

		$this->add_element( $data );
		return $this;
	}

	/**
	 * Vlozi Text area
	 * @param string $name
	 * @param string $label - nadpis
	 * @param int $cols - sloupce
	 * @param int $rows - radky
	 * @return \Form\Form
	 */
	public function addTextArea($name, $label = null, $cols = 30, $rows = 9)
	{
		$data = array(
			 'metadata' => array(
				  'type' => 'textarea',
				  'label' => $label
			 ),
			 'data' => array(
				  'name' => $name,
				  'id' => 'frm_' . $name,
				  'rows' => $rows,
				  'cols' => $cols,
			 )
		);

		$this->add_element( $data );
		return $this;
	}

	public function addRadio($name, $value, $checked = false, $group = "group1", $label = null)
	{
		$data = array(
			 'metadata' => array(
				  'type' => 'radio',
				  'label' => $label,
				  'group' => $group
			 ),
			 'data' => array(
				  'name' => $name,
				  'value' => $value,
				  'checked' => $checked != true ? false : true
			 )
		);
		$this->add_element( $data );

		return $this;
	}

	/**
	 * 
	 * @param string/HTMLConstructor $object
	 * @return \Form\Form
	 */
	public function addHook($string, $label = '')
	{
		if ( $string instanceof \HTMLconstructor )
		{
			$string = $string->generate();
		}

		$data = array(
			 'metadata' => array(
				  'type' => 'hook',
				  'label' => $label
			 ),
			 'data' => array(
				  'name' => md5( $string ),
				  'value' => $string
			 )
		);
		$this->add_element( $data );
		return $this;
	}

	public function addHidden($name, $value)
	{
		$data = array(
			 'metadata' => array(
				  'type' => 'hidden'
			 ),
			 'data' => array(
				  'name' => $name,
				  'value' => $value,
				  'id' => 'frm_' . $name
			 )
		);

		$this->add_element( $data );
		return $this;
	}

	/**
	 * Prida rozeviraci menu. Naplni se pres prikaz addDropdownOption
	 * @param String $name
	 * @param String $label
	 * @param String/Array $default_option_name - nazev polozky, ktera tam bude defaultne
	 * @return \Form 
	 */
	public function addDropdown($name, $label = null, $default_option_name = null)
	{
		$data = array(
			 'metadata' => array(
				  'type' => 'dropdown',
				  'label' => $label
			 ),
			 'data' => array(
				  'name' => $name,
				  'default' => $default_option_name,
				  'options' => array()
			 )
		);
		$this->add_element( $data );

		return $this;
	}

	/**
	 * Nastavi defaultni zobrazovani textu o daneho dropdown menu
	 * @param String $dropdown_element_name
	 * @param String $option_name 
	 */
	public function setDropdownDefaultOptionName($option_name, $dropdown_element_name = null)
	{
		if ( $dropdown_element_name == null )
			$element = &$this->active_element;
		else
			$element = &$this->getElement( $dropdown_element_name );
		$element['data']['default'] = $option_name;
		return $this;
	}

	/**
	 * Polozka pro rozeviratelny dropdown
	 * @param String $dropdown_element_name - nazev dropdownu, pro ktery se aplikuje
	 * @param String $value - jake hodnoty bude dana polozka nabyvat
	 * @param Strin $text - viditelny text polozky
	 */
	public function addDropdownOption($value, $text, $dropdown_element_name = null)
	{
		if ( $dropdown_element_name == null )
			$element = &$this->active_element;
		else
			$element = &$this->getElement( $dropdown_element_name );

		$element['data']['options'][$value] = $text;
		return $this;
	}

	public function addCheckbox($name, $value, $checked = false, $label = null)
	{
		$data = array(
			 'metadata' => array(
				  'type' => 'checkbox',
				  'label' => $label
			 ),
			 'data' => array(
				  'name' => $name,
				  'value' => $value,
				  'checked' => $checked != true ? false : true
			 )
		);
		$this->add_element( $data );

		return $this;
	}

	public function addGroup($text)
	{
		$data = array(
			 'metadata' => array(
				  'type' => 'group'
			 ),
			 'data' => array(
				  'value' => $text
			 )
		);

		$this->add_element( $data );
		return $this;
	}

	/**
	 * Nastavi url adresu formulare. Taktez se muze zadat do
	 * konstruktoru tridy
	 * @param string $url
	 * @return \Form\Form
	 */
	public function setDestination($url)
	{
		$this->submit_url = $url;
		return $this;
	}

	public function setSubmit($name, $text)
	{
		$data = array(
			 'metadata' => array(
				  'type' => 'submit'
			 ),
			 'data' => array(
				  'value' => $text,
				  'name' => $name
			 )
		);
		$this->add_element( $data );
		return $this;
	}

	/**
	 * Prida html element k danemu elementu.
	 * Pokud jako element_name napisete "FORM", jednotlivy atribut se bude
	 * vstahovat k celemu formulari. - DEPRECATED!
	 * pokud chcete upravovat atribut primo na formulari, pouzijte rovnou
	 * $this->add_form_attribute()

	 * @param String $attribute - nazev atributu ("id","class" ... "onclick")
	 * @param String $value - hodnota atributu
	 * @param String $element_name - nazev elementu - POKUD neni nazev elementu
	 * urcen, pouzije se posledni vlozeny element.
	 * @return \Form 
	 */
	public function setAttribute($attribute, $value, $element_name = null)
	{
		if ( strtoupper( $element_name ) == 'FORM' )
		{
			$this->set_form_attribute( $attribute, $value );
		}
		else
		{
			if ( $element_name == null )
			{
				$element = & $this->active_element;
				$element['data'][$attribute] = $value;
			}
			else
			{
				$element = & $this->getElement( $element_name );
				$element['data'][$attribute] = $value;
			}
		}
		return $this;
	}

	/**
	 * Nastavi pravidlo (specialni labely do message: 
	 * %label%, %name%, %argument% )
	 * @param type $element_name - pokud zustane prazdny, pouzije se prave
	 * aktivni element.
	 * @param type $rule
	 * @param type $message
	 * @param type $argument
	 * @return \Form 
	 */
	public function setRule($rule, $message, $argument = null, $element_name = null)
	{
		if ( $element_name == null )
			$element = & $this->active_element;
		else
			$element = & $this->getElement( $element_name );

		if ( $this->isLangFileInUse() )
			$message = $this->ci->lang->line( $message );

		$co = array("%label%", "%name%", "%argument%");
		$zaco = array($element['metadata']['label'], $element['data']['name'], $argument);

		$message = str_replace( $co, $zaco, $message );

		$element['data']['data-validation-' . $rule] = form_prep( $message );
		if ( $argument != null )
		{
			$element['data']['data-validation-' . $rule] .= '::' . $argument;
		}

		return $this;
	}

	/**
	 * Nastavi hodnotu prvku pro dany nazev elementu.
	 * Paklize $element_name je array - provede se nastaveni
	 * hodnoty prvku pro vsechny prvky urcene v array
	 * (array(nazev_elementu => hodnota, ...); )
	 * 
	 * Paklize $element_name je objekt, zkusi se prevest na 
	 * pole. Vhodne, pokud se prvky jmenujou stejne jako se jmenujou sloupce
	 * z db
	 * @param type $element_name
	 * @param type $value
	 * @return \Form 
	 */
	public function setValue($value = null, $element_name = null)
	{
		if ( $element_name == null && $value == null )
			show_error( "class Form: set_value() : nebyl urcen ani jeden argument." );

		if ( is_object( $value ) )
			$element_name = get_object_vars( $value );

		if ( is_array( $element_name ) )
		{

			foreach ( $element_name as $key => $value )
			{
				if ( $value == '' )
					continue;
				$e = &$this->getElement( $key );

				if ( $e == null )
					continue;

				$e['data']['value'] = $value;
			}
		} else
		{
			if ( $element_name == null )
				$e = & $this->active_element;
			else
				$e = & $this->getElement( $element_name );
			$e['data']['value'] = $value;
		}

		return $this;
	}

	/**
	 * Otevre formular. Pokud generujete cely formular, tato funkce
	 * je automaticky volana a vy ji nemusite volat.
	 * Pokud generujete jen casti formulare (rucne), pote je potreba
	 * tuto funkci zavolat.
	 * @return \Form 
	 */
	public function open()
	{
		return form_open( $this->submit_url, (count( $this->form_attributes ) == 0 ? null : $this->form_attributes ) ) . PHP_EOL;
	}

	/**
	 * Zavre formular. Pokud generujete cely formular, neni treba
	 * tuto funkci volat. 
	 */
	public function close()
	{
		return form_close() . PHP_EOL;
	}

	/**
	 * Zvoli aktivn jiny element, ktery muze byt pouzit pro pridani atributu
	 * ci validacnich pravidel
	 * @param type $element_name
	 * @return \Form
	 */
	public function set_active_element($element_name)
	{
		$this->active_element = & $this->getElement( $element_name );
		return $this;
	}

	public function validation_disable($disable = true)
	{
		$this->set_form_attribute( "data-validationdisable", $disable ? "true" : "false"  );
	}

	/**
	 * Prida do hlavicky formulare atribut $name s hodnotou $value.
	 * @param String $name - nazev atributu
	 * @param String $value - hodnota atributu
	 * @return \Form
	 */
	public function set_form_attribute($name, $value)
	{
		$this->form_attributes[$name] = $value;
		return $this;
	}

	/**
	 * Ulozi element
	 * @param array $data
	 * @return \Form
	 */
	private function add_element($data, $do_not_replace = false)
	{
		$founded = false;
		if ( !$do_not_replace )
		{
			foreach ( $this->elements as &$value )
			{
				if ( $value['data']['name'] == $data['data']['name'] && strpos( $data['data']['name'], '[]' ) === false )
				{
					$value = $data;
					$founded = true;
					$this->active_element = &$value;
				}
				unset( $value );
			}
		}
		if ( !$founded )
		{
			$this->elements[] = $data;
			$this->active_element = &$this->elements[count( $this->elements ) - 1];
		}




		return $this;
	}

	/**
	 * Vrati vsechny elementy dle typu (popr. i podle jmena
	 * @param String $type - filtrace dle typu elementu (checkbox aj.)
	 * @param String $element_name = vyfiltruje i podle nazvu
	 * @return Array 
	 */
	public function getElementsByType($type, $element_name = null)
	{
		$return = array();
		foreach ( $this->elements as $element )
		{
			if ( $element['metadata']['type'] == $type &&
					  (($element_name != null && $element['data']['name'] == $element_name) ||
					  $element_name == null) )
				$return[] = $element;
		}

		return $return;
	}

	/**
	 * Nastavi formularove textove prvky, aby si brali text
	 * z jazykoveho balicku. (tzn misto samotneho textu pisete
	 * odkazy na text v balickach)
	 * @param type $turn_on
	 * @return type
	 */
	public function useLangFile($turn_on = TRUE)
	{
		$this->use_lang_file = $turn_on;
		return $this;
	}

	/**
	 * Zjisti, jestli se vyuziva jazykovy balicek, nebo ne
	 * @return boolean
	 */
	public function isLangFileInUse()
	{
		return $this->use_lang_file;
	}

	/**
	 * Paklize formular obsahuje chybu, misto vypsani chyby tradicnim
	 * zpusobem se zavola javascriptova funkce urcena z teto funkce.
	 * Jako parametry se odeslou jednotlive chyby a jejich reference na
	 * jednotlive inputy.
	 * @param string $func_name - nazev funkce, ktera se zavola
	 */
	public function set_onError_callback($func_name)
	{
		$this->set_form_attribute( self::VALIDATION_ONERRROR_CALLBACK, $func_name );
		return $this;
	}

	/**
	 * Pokud chcete ovlivnit, KAM se vypisou chyby, tato funkce formulari rekne, jake
	 * ID prvku se ma chybami naplnit.
	 * @param String $element_id - identifikator elementu (#id, .classname atd.)
	 */
	public function set_validation_output($element_id)
	{
		$this->set_form_attribute( self::VALIDATION_RENDER_ELEMENT, $element_id );
		return $this;
	}

	/**
	 * Vrati element dle jmena
	 * @param String $elementName
	 * @return void 
	 */
	public function &getElement($elementName)
	{
		foreach ( $this->elements as &$element )
		{
			if ( $element['data']['name'] == $elementName )
				return $element;
		}
		$n = null;
		return $n;
	}

	/**
	 * Vrati vsechny elementy formulare
	 * @return array
	 */
	public function &getElements()
	{
		return $this->elements;
	}

	/**
	 * Ulozi vsechny elementy formulare
	 * @return array
	 */
	public function setElements($elements)
	{
		$this->elements = $elements;
		return $this;
	}

}

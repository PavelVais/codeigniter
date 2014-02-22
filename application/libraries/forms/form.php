<?php

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
	 * Paklize se pri generovani na misto nazvu elementu napise tato konstanta,
	 * automaticky se zvoli posledni zvoleny element. 
	 * Vhodne pri vykresleni pouze LABELU a pote pouze INPUTU. (setri zdroje.)
	 * @notImplementedYet
	 */
	const LAST_ELEMENT = "@last_element";

	/**
	 * Vygeneruje element bez labelu
	 */
	const WITHOUT_LABEL = 1;

	/**
	 * Vygeneruje pouze label pridruzeny k danemu elementu
	 */
	const ONLY_LABEL = 2;

	/**
	 * Dany element vygeneruje bez wrapperu 
	 */
	const WITHOUT_WRAPPER = 4;

	/**
	 * Parametr otevirajici wrapper.  V defaultnim pripade vypisuje DL znacku.
	 * Do neho se pote vkladaji vsechny labely a inputy. Ke konci formulare
	 * se musi zavolat parametr CLOSE_WRAPPER
	 */
	const OPEN_WRAPPER = 8;

	/**
	 * CLOSE_WRAPPER zavira DL znacku. Mel by se volat u posledniho formularoveho prvku. 
	 */
	const CLOSE_WRAPPER = 16;

	/**
	 * Vypise label i kdyz neni urcen v metadata. Slouzi pro spravne zarovnani
	 * prvku. Diky tomuto se vypise DT ikdyz neni label urcen (u submitu napr.)
	 * a DD prvek neni odskocen od ostatnich.
	 */
	const PRINT_LABEL = 32;

	/**
	 * Hlavni konstruktor
	 *  
	 */
	public function __construct($destination_url = null)
	{
		$this->ci = & get_instance();

		$this->wrapper = new Form_Wrapper();
		$this->wrapper->setControlWrapper( 'dl' );
		$this->wrapper->setLabelWrapper( 'dt' );
		$this->wrapper->setElementWrapper( 'dd' );

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
				  'id' => $name,
				  'maxlength' => $maxSize,
				  'size' => $size
			 )
		);

		$this->add_element( $data );
		return $this;
	}

	public function addPassword($name, $label = null, $size = 20, $maxSize = 20)
	{
		$data = array(
			 'metadata' => array(
				  'type' => 'password',
				  'label' => $label
			 ),
			 'data' => array(
				  'name' => $name,
				  'id' => $name,
				  'maxlength' => $maxSize,
				  'size' => $size
			 )
		);

		$this->add_element( $data );
		return $this;
	}

	public function addTextArea($name, $label = null, $cols = 30, $rows = 9)
	{
		$data = array(
			 'metadata' => array(
				  'type' => 'textarea',
				  'label' => $label
			 ),
			 'data' => array(
				  'name' => $name,
				  'id' => $name,
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

	public function addHidden($name, $value)
	{
		$data = array(
			 'metadata' => array(
				  'type' => 'hidden'
			 ),
			 'data' => array(
				  'name' => $name,
				  'value' => $value
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
	public function set_attribute($attribute, $value, $element_name = null)
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
	public function set_rule($rule, $message, $argument = null, $element_name = null)
	{
		if ( $element_name == null )
			$element = & $this->active_element;
		else
			$element = & $this->getElement( $element_name );

		if ( $this->use_lang_file() )
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
	public function set_value($value = null, $element_name = null)
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
	 * @deprecated since version 1.2
	 */
	public function open_form()
	{
		echo $this->open();
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
		echo form_open( $this->submit_url, (count( $this->form_attributes ) == 0 ? null : $this->form_attributes ) ) . PHP_EOL;
	}

	/**
	 * Zavre formular. Pokud generujete cely formular, neni treba
	 * tuto funkci volat. 
	 * @deprecated since version 1.2
	 */
	public function close_form()
	{
		echo $this->close();
	}

	/**
	 * Zavre formular. Pokud generujete cely formular, neni treba
	 * tuto funkci volat. 
	 */
	public function close()
	{
		echo form_close() . PHP_EOL;
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

	public function generate($elements = null, $arguments = 0)
	{
		$manual_generation = TRUE;
		if ( $elements == null )
		{
			$manual_generation = FALSE;
			echo $this->open(); //automaticko generovani - otevre si form sam
		}

		if ( $manual_generation )
		{
			//= Jde o manualni generovani formu. Je potreba ziskat veskere
			//= info o elementech, ktere si uzivatel zadal. ($elements)
			if ( !is_array( $elements ) )
				$elements = array($elements);

			foreach ( $elements as &$e )
			{
				$e = $this->getElement( $e );
			}
		}
		else
			$elements = $this->elements;

		$index = 0;
		$el_count = count( $elements );

		//= Vygenerovani vsech form. prvku mimo hiddenu a submitu
		foreach ( $elements AS $element )
		{
			++$index;
			$attributes = $arguments;

			if ( !$manual_generation && ($element['metadata']['type'] == "hidden" || $element['metadata']['type'] == "submit") )
			//= preskocit hiddeny a submity pokud se jedna o automaticke generovani
				continue;

			if ( $index == 1 && !$manual_generation )
				$attributes += Form::OPEN_WRAPPER;

			if ( $index == $el_count && !$manual_generation )
				$attributes += Form::CLOSE_WRAPPER;

			$this->_print_element( $element, $attributes );
		}

		if ( !$manual_generation )
		{
			//= Pokud se formular generuje automaticky, vypise
			//= se na konec hidden elementy a submit
			foreach ( $elements AS $element )
			{
				if ( $element['metadata']['type'] == "hidden" )
					$this->_print_element( $element, self::WITHOUT_WRAPPER );

				if ( $element['metadata']['type'] == "submit" )
					$this->_print_element( $element, self::PRINT_LABEL );
			}
			//= A zavre se formular
			echo $this->wrapper->wrap( "", Form_Wrapper::CONTROL_WRAPPER, true, false );
			echo $this->close();
		}

		//= uzavreni fieldsetu, paklize je otevren
		if ( !$this->fieldset_opened )
			form_fieldset_close();

		if ( $element == null )
		{
			echo $this->close();
		}
	}

	private function _print_element($element, $attributes = null)
	{

		//= Vygenerovani formularoveho prvku do promenne
		$data = $element['data'];
		$metadata = $element['metadata'];
		if ( isset( $metadata['label'] ) && $this->use_lang_file )
		{
			$metadata['label'] = $this->ci->lang->line( $metadata['label'] );
		}


		switch ($metadata['type'])
		{
			default:
				$string = null;
				break;
			case 'hidden':
				$string = form_hidden( $data['name'], $data['value'] );
				break;
			case 'submit':
				if ( $this->use_lang_file )
					$data['value'] = $this->ci->lang->line( $data['value'] );
				$string = form_submit( $data );
				break;
			case 'input':
				$string = form_input( $data );
				break;
			case 'checkbox':
				$string = form_checkbox( $data );
				break;
			case 'radio':
				$data['name'] = $metadata['group'];
				$string = form_radio( $data );
				break;
			case 'textarea':
				$string = form_textarea( $data );
				break;
			case 'dropdown':
				$string = form_dropdown( $data["name"], $data['options'], $data['default'] );
				break;
			case 'group':

				if ( !$this->fieldset_opened )
					form_fieldset_close();

				$string = form_fieldset( $data['value'] );
				$this->fieldset_opened = true;

				break;
			case 'password':
				$string = form_password( $data );
				break;
		}
		//= Pokud je zde atribut OPEN_WRAPPER, vypise se oteviraci wrapper
		if ( $this->_is_divisble( $attributes, self::OPEN_WRAPPER ) )
		{
			echo $this->wrapper->wrap( "", Form_Wrapper::CONTROL_WRAPPER, false );
		}


		//=== Vykresleni formularoveho prvku ==
		//= Vykresluje se label
		if ( (!$this->_is_divisble( $attributes, self::WITHOUT_LABEL ) && isset( $metadata['label'] )) || !isset( $metadata['label'] ) && $this->_is_divisble( $attributes, self::PRINT_LABEL ) )
		{
			if ( !$this->_is_divisble( $attributes, self::PRINT_LABEL ) && $metadata['label'] != '' )
			{
				if ( $this->_is_divisble( $attributes, self::WITHOUT_WRAPPER ) )
					echo form_label( $metadata['label'], $data['name'] ) . PHP_EOL;
				else
					echo $this->wrapper->wrap( form_label( $metadata['label'], $data['name'] ), Form_Wrapper::LABEL_WRAPPER ) . PHP_EOL;
			} else
			{
				if ( !$this->_is_divisble( $attributes, self::WITHOUT_WRAPPER ) )
					echo $this->wrapper->wrap( '', Form_Wrapper::LABEL_WRAPPER ) . PHP_EOL;
			}
		}

		//= Vykresluje se formularovy prvek
		if ( !$this->_is_divisble( $attributes, self::ONLY_LABEL ) && $string != null )
			if ( $this->_is_divisble( $attributes, self::WITHOUT_WRAPPER ) )
				echo $string . PHP_EOL;
			else
				echo $this->wrapper->wrap( $string, Form_Wrapper::ELEMENT_WRAPPER ) . PHP_EOL;


		//= Pokud je zde atribut CLOSE_WRAPPER, vypise se zaviraci wrapper /DL defaultne
		if ( $this->_is_divisble( $attributes, self::CLOSE_WRAPPER ) )
		{
			echo $this->wrapper->wrap( "", Form_Wrapper::CONTROL_WRAPPER, true, false );
		}
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

	public function setWrapper($control = null, $label = null, $element = null)
	{
		if ( $control != null )
			$this->wrapper->setControlWrapper( $control );
		if ( $label != null )
			$this->wrapper->setLabelWrapper( $label );
		if ( $element != null )
			$this->wrapper->setElementWrapper( $element );
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
	public function use_lang_file($turn_on = TRUE)
	{
		$this->use_lang_file = $turn_on;
		return $this;
	}

	public function get_lang_usage()
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
	 * Prida (prepise) cely element
	 * @param array $data
	 * @return \Form
	 */
	private function setElement($element_name, $element)
	{
		for ( $index = 0; $index < count( $this->elements ); $index++ )
		{
			if ( $this->elements[$index]['data']['name'] == $element_name )
			{
				$this->elements[$index] = $element;
				break;
			}
		}
		return $this;
	}

	/**
	 * Funkce ktera zkouma, zdali dane cislo ($factor) je delitelem cisla $number.
	 * @param int $number - delenec
	 * @param int $factor - delitel
	 * @return boolean - TRUE - je delitelny, FALSE - neni delitelny
	 */
	private function _is_divisble($number, $factor)
	{
		if ( $number == null )
			return FALSE;
		if ( $factor == 0 )
			return FALSE;
		return ((($number & $factor) == 0) ? FALSE : TRUE);
	}

}

class FormGenerator
{

	/**
	 *
	 * @var Form
	 */
	static $form;
	static $options;
	static $ci;

	/**
	 * Funkce na nacteni formulare, pomoci ktereho muze tento
	 * generator vygenerovat formular.
	 * @param Form $form
	 */
	public static function setup(Form $form)
	{
		self::$form = $form;
		self::$options = array(
			 'is_first' => TRUE
		);
		self::$ci = & get_instance();
	}

	public static function open()
	{
		self::$form->open();
		self::$options['is_first'] = false;
	}

	public static function close()
	{
		self::$form->close();
	}

	/**
	 * Vygeneruje urceny formularovy prvek
	 * @param String $elementName
	 */
	public static function generate($elementName, $withLabel = true)
	{
		if ( self::$options['is_first'] )
		{
			self::$options['is_first'] = false;
			echo self::$form->open();
		}
		$element = is_array( $elementName ) ? $elementName : self::$form->getElement( $elementName );

		if ( $withLabel && !isset( $element['metadata']['label_printed'] ) && isset( $element['metadata']['label'] ) )
			self::generateLabel( $elementName );

		if ( $element['metadata']['type'] == "radio" )
			$element['data']['name'] = $element['metadata']['group'];

		$function = 'form_' . $element['metadata']['type'];

		if ( $function == "form_hidden" )
			echo $function( $element['data']['name'], $element['data']['value'] );
		else
			echo $function( $element['data'] );
	}

	public static function generateHidden()
	{
		$hidden = self::$form->getElementsByType( 'hidden' );
		if ( count( $hidden ) != 0 )
			foreach ( $hidden as $value )
			{
				self::generate( $value['data']['name'], false );
			}
	}

	/**
	 * Vygeneruje label pro dany formularovy element
	 * @param String $elementName
	 * @return String
	 */
	public static function generateLabel($elementName)
	{
		$element = & self::$form->getElement( $elementName );
		$element['metadata']['label_printed'] = TRUE;
		echo form_label( self::$form->get_lang_usage() ? self::$ci->lang->line( $element['metadata']['label'] ) : $element['metadata']['label'], $element['data']['name'] );
	}

	/**
	 * Vygeneruje cely formular
	 */
	private static function generateAll()
	{
		self::$form->generate();
	}

	/**
	 * Vygeneruje submitovaci tlacitko
	 */
	public static function generateSubmit()
	{
		$submit = self::$form->getElementsByType( 'submit' );
		$submit[0]['data']['value'] = self::$form->get_lang_usage() ? self::$ci->lang->line( $submit[0]['data']['value'] ) : $submit[0]['data']['value'];
		echo form_submit( $submit[0]['data'] );
	}

}
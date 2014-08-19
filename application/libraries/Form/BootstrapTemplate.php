<?php

namespace Form;

class BootstrapTemplate implements TemplateInterface
{

	private $options;
	private $tempOptions;
	private $currentElement;

	const HORIZONTAL = 'form-horizontal';
	const INLINE = 'form-inline';
	const STATIC_TEXT = 'form-control-static';
	const SUCCESS = 'has-success';
	const ERROR = 'has-error';
	const OPTION_LAYOUT = 'layout';
	const OPTION_ELEMENT_TYPE = 'element_type';
	const OPTION_ROW_TYPE = 'group_type';
	const OPTION_LABEL_CLASS = 'label_class';
	const OPTION_INPUT_CLASS = 'input_class';

	public function __construct()
	{
		$this->setOption( self::OPTION_LAYOUT, self::HORIZONTAL )
				  ->setOption( self::OPTION_INPUT_CLASS, 'col-sm-9' )
				  ->setOption( self::OPTION_LABEL_CLASS, 'col-sm-3' );


		$this->tempOptions[0] = array();
	}

	public function generate($element, $withLabel = true)
	{
		$this->currentElement = $element;
		$htmlFormGroup = \HTML\Element::open( 'div' )->addAttribute( 'class', 'form-group' )->setFirstIndent( 2 );

		if ( ($opt = $this->getOption( self::OPTION_ROW_TYPE )) !== '' )
		{
			$htmlFormGroup->appendToAttribute( 'class', $opt );
		}
		$e = $this->generateElement( $element );

		if ( $withLabel )
		{
			$label = $this->generateLabel( $element );
		}
		else
		{
			$e->appendToAttribute( 'class', 'col-sm-offset-3' );
		}

		unset( $this->tempOptions[0] );
		return $htmlFormGroup->append( $label )
							 ->append( $e )->generate();
	}

	/**
	 * 
	 * @param array $element
	 * @return \HTMLconstructor
	 */
	public function generateElement($element)
	{
		$element = \HTML\Element::open( 'div' )->addAttribute( 'class', $this->getOption( self::OPTION_INPUT_CLASS ) )
				  ->appendString( $element['html'] );

		return $element;
	}

	/**
	 * Predpripravi formularove prvky
	 * @param array &$element
	 * @return array
	 */
	public function prepareElement(&$element)
	{
		if ( isset( $element['data']['class'] ) )
		{
			$element['data']['class'] = $element['data']['class'] . ' form-control';
		}
		else
		{
			$element['data']['class'] = 'form-control';
		}
		return $element;
	}

	public function prepareForm(Form &$form)
	{
		$form->set_form_attribute( 'class', $this->getOption( self::OPTION_LAYOUT ) );
		return $form;
	}

	/**
	 * 
	 * @param type $element
	 * @return \HTMLconstructor
	 */
	public function generateLabel($element)
	{
		//'<label class="col-sm-2 control-label" for="inputEmail3">Email</label>';
		return $labelConstructor = \HTML\Element::open( 'label' )
				  ->addAttribute( 'for', 'frm_' . $element['data']['name'] )
				  ->addAttribute( 'class', $this->getOption( self::OPTION_LABEL_CLASS ) )
				  ->appendToAttribute( 'class', 'control-label' )
				  ->appendString( $element['metadata']['label'] );
	}

	public function setOnce($name, $value, $forElement = null)
	{
		$this->tempOptions[($forElement == null ? 0 : $forElement)][$name] = $value;
		return $this;
	}

	public function setOption($name, $value)
	{
		$this->options[$name] = $value;
		return $this;
	}

	/**
	 * Vrati nastaveni. 
	 * @param string $name
	 * @return \Form\BootstrapTemplate
	 */
	private function getOption($name)
	{

		if ( isset( $this->tempOptions[$this->currentElement['data']['name']][$name] ) )
		{
			return $this->tempOptions[$this->currentElement['data']['name']][$name];
		}
		if ( isset( $this->tempOptions[0][$name] ) )
		{
			return $this->tempOptions[0][$name];
		}
		elseif ( isset( $this->options[$name] ) )
		{
			return $this->options[$name];
		}
		return '';
	}

}

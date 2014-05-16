<?php

namespace Form;

class Generator
{

	/** @var TemplateInterface */
	public $template;
	public $form;
	private $formData;
	private $isOpenned = false;
	private $dontClose = false;
	private $pointer = -1;
	private $ci;

	public function __construct(Form $form)
	{
		$this->form = $form;
		$this->translate();

		$this->ci = & get_instance();
		$this->formData = $form->getElements();
	}

	/**
	 * Nastavi temaplte, podle ktereho se budou vykreslovat prvky
	 * @param \Form\TemplateInterface $formTemplate
	 */
	public function setTemplate(TemplateInterface $formTemplate)
	{
		$this->template = $formTemplate;
	}

	/**
	 * Vyjme z generovani urcity objekt
	 * @param string $elementName - vyjme objekt dle nazvu
	 * @param string $elementType - vyjme celistvou skupinu objektu
	 * @return \Form\Generator
	 */
	public function exclude($elementName, $elementType = '')
	{
		foreach ( $this->formData as $key => $element )
		{
			if ( $element['data']['name'] == $elementName || $element['metadata']['type'] == $elementType )
				unset( $this->formData[$key] );
		}
		return $this;
	}

	/**
	 * Nezavre formular po vykresleni submitu
	 */
	public function dontClose()
	{
		$this->dontClose = true;
	}

	/**
	 * Hlavni funkce generatoru - vygenerovani celistveho 
	 * formulare.
	 * @return string
	 */
	public function generate()
	{
		$str = '';
		$str .= $this->open();
		$this->exclude( null, 'hidden' );
		$this->exclude( null, 'submit' );

		foreach ( $this->formData as &$element )
		{
			$this->template->prepareElement( $element );
			$element['html'] = $this->getHTMLFormElement( $element );
			$str .= $this->template->generate( $element );
		}

		$submit = $this->form->getElementsByType( 'submit' );

		if ( !$this->dontClose )
		{
			if ( !empty( $submit ) )
			{
				$str .= $this->generateHidden();
				$submit[0]['html'] = $this->getHTMLFormElement( $submit[0] );
				$str .= $this->template->generate( $submit[0], false );
			}

			$str .= $this->close();
		}
		$this->flushMemory();
		return $str;
	}

	private function flushMemory()
	{
		//= Vygeneroval se cely form, muzeme ho odvolat z db
		if ( !$this->dontClose )
		{
			$this->form = null;
			$this->template = null;
			unset( $this->ci );
		}
		unset( $this->formData );
	}

	private function translate($elements)
	{
		if ( !$this->form->isLangFileInUse() )
			return;

		$e = $this->form->getElements();
		foreach ( $e as &$element )
		{
			switch ($element['metadata']['type'])
			{
				case 'hidden':
					continue 2;
				case 'submit':
					$elements['data']['value'] = $this->ci->lang->line( $element['data']['value'] );
					break;
				default:
					$element['metadata']['label'] = $this->ci->lang->line( $element['metadata']['label'] );
					break;
			}
		}
		$this->form->setElements( $e );
		return $this;
	}

	public function assignHTMLforElements()
	{
		foreach ( $this->formData as &$element )
		{
			$this->element = $this->getHTMLFormElement( $element );
		}
		return $this;
	}

	private function getHTMLFormElement($element)
	{
		$type = $element['metadata']['type'];
		$function = 'form_' . $type;

		switch ($type)
		{
			case 'hidden':
				return $function( $element['data']['name'], $element['data']['value'], $element['data'] );
			case 'dropdown':
				return $function( $element['data']["name"], $element['data']['options'], $element['data']['default'], $element['data'] );
			case 'submit':
				return $function( $element['data'] );
			case 'hook':
				return $element['data']['value'];
			case 'radio':
				$element['data']['name'] = $element['metadata']['group'];
			default:
				return $function( $element['data'] );
		}

		return $this;
	}

	public function open()
	{
		if ( !$this->isOpenned )
		{
			$this->template->prepareForm( $this->form );
			$this->isOpenned = true;
			return $this->form->open();
		}

		return '';
	}

	public function close()
	{
		return $this->form->close();
	}

	public function next()
	{

		if ( isset( $this->formData[$this->pointer + 1] ) )
		{
			return $this->pointer++;
		}
		return false;
	}

	/**
	 * @todo Potreba dopsat! spatne se vygeneruji submity a hiddeny
	 */
	public function generateBySteps()
	{
		$p = $this->next();
		if ( !$p )
		{
			show_error( 'Form: generateBySteps() No more steps!' );
		}
		else
		{
			$this->template->generate( $this->formData[$p] );
		}
	}

	public function generateHidden()
	{
		$hiddenElements = $this->form->getElementsByType( 'hidden' );
		$str = '';
		foreach ( $hiddenElements as $h )
		{
			$str .= $this->generateElement( $h );
		}
		return $str;
	}

	public function generateSubmit()
	{
		$submit = $this->form->getElementsByType( 'submit' );
		$submit[0]['data']['value'] = $this->form->isLangFileInUse() ? $this->ci->lang->line( $submit[0]['data']['value'] ) : $submit[0]['data']['value'];

		return form_submit( $submit[0]['data'] );
	}

	public function generateLabel($elementName)
	{
		$element = $this->form->getElement( $elementName );
		return form_label( $this->form->isLangFileInUse() ? $this->ci->lang->line( $element['metadata']['label'] ) : $element['metadata']['label'], 'frm_' . $element['data']['name'] );
	}

	public function generateLabelString($elementName)
	{
		$element = $this->form->getElement( $elementName );
		return $this->form->isLangFileInUse() ? $this->ci->lang->line( $element['metadata']['label'] ) : $element['metadata']['label'];
	}

	public function generateElement($element)
	{
		if ( !is_array( $element ) )
		{
			$element = $this->form->getElement( $element );
		}
		return $this->getHTMLFormElement( $element );
	}

}

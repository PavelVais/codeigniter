<?php

namespace Form;

abstract class Template implements TemplateInterface
{

	protected $options;
	protected $tempOptions;
	protected $currentElement;

	

	public function __construct()
	{
		$this->tempOptions[0] = array();
	}

	/**
	 * Nastavi nastaven pro urcity element. Pokud neni urcen, nastavi se pro
	 * NASLEDUJICI
	 * @param string $name
	 * @param string/int $value
	 * @param string $forElement
	 * @return \Form\Template
	 */
	public function setOnce($name, $value, $forElement = null)
	{
		$this->tempOptions[($forElement == null ? 0 : $forElement)][$name] = $value;
		return $this;
	}

	/**
	 * Nastaveni nastaveni, ktere se vstahuje pro vsechny generovane elementy.
	 * setOnce ma kazdopadne vyssi vahu
	 * @param type $name
	 * @param type $value
	 * @return \Form\Template
	 */
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
	protected function getOption($name)
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

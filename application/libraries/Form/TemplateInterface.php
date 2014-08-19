<?php

namespace Form;

interface TemplateInterface
{

	public function generate($element, $withLabe = true);

	public function prepareElement(&$element);

	public function prepareForm(Form &$form);

	/**
	 * Nastavi urcitou vlastnost
	 */
	public function setOption($name, $value);

	/**
	 * Nastavi vlastnost pouze pro nasledujici element
	 */
	public function setOnce($name, $value,$forElement);
}

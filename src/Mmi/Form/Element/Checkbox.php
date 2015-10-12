<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Form\Element;

/**
 * Element checkbox
 * @method Checkbox setValue($value) ustawia wartość
 */
class Checkbox extends ElementAbstract {

	/**
	 * Konstruktor ustawia kolejność i opcje
	 * @param string $name
	 */
	public function __construct($name) {
		parent::__construct($name);
		$this->setRenderingOrder(['fetchBegin', 'fetchField', 'fetchLabel', 'fetchDescription', 'fetchErrors', 'fetchEnd'])
			->setLabelPostfix('')
			->setValue(1);
	}

	/**
	 * Render pola
	 * @return string
	 */
	public function fetchField() {
		return '<input type="checkbox" ' . $this->_getHtmlOptions() . '/>';
	}
	
	/**
	 * Ustawia zaznaczenie
	 * @return \Mmi\Form\Element\Checkbox
	 */
	public function setChecked($checked = true) {
		return $checked ? $this->setOption('checked', '') : $this->unsetOption('checked');
	}
	
}

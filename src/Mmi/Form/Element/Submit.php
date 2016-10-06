<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Form\Element;

/**
 * Element submit
 */
class Submit extends ElementAbstract {

	/**
	 * Konstruktor, ustawia nazwę pola i opcje
	 * @param string $name nazwa
	 */
	public function __construct($name) {
		parent::__construct($name);
		$this->setIgnore()
			->setRenderingOrder(['fetchBegin', 'fetchField', 'fetchErrors', 'fetchEnd']);
	}

	/**
	 * Buduje pole
	 * @return string
	 */
	public function fetchField() {
		//labelka jako value
		if ($this->getLabel()) {
			$this->setValue($this->getLabel());
		}
		return '<input type="submit" ' . $this->_getHtmlOptions() . '/>';
	}

}

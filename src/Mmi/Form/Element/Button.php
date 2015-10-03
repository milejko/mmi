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
 * Klasa guzika
 */
class Button extends ElementAbstract {

	/**
	 * Ignorowanie tego pola, inna kolejnośc renderowania
	 */
	public function __construct($name) {
		parent::__construct($name);
		$this->setIgnore();
		$this->getRenderer()->setRenderingOrder(['fetchBegin', 'fetchField', 'fetchErrors', 'fetchEnd']);
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
		return '<input type="button" ' . $this->_getHtmlOptions() . '/>';
	}

}

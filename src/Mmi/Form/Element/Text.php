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
 * Element pole tekstowe
 */
class Text extends ElementAbstract {

	/**
	 * Rendering pola tekstowego
	 * @return string
	 */
	public function fetchField() {
		$filter = $this->_getFilter('input');
		$this->setValue($filter->filter($this->getValue()));
		return '<input type="text" ' . $this->_getHtmlOptions() . '/>';
	}

}

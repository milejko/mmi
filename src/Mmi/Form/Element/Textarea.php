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
 * Element textarea
 */
class Textarea extends ElementAbstract {

	/**
	 * Buduje pole
	 * @return string
	 */
	public function fetchField() {
		$value = (new \Mmi\Filter\Input())->filter($this->getValue());
		$this->unsetOption('value');
		return '<textarea ' . $this->_getHtmlOptions() . '>' . $value . '</textarea>';
	}

}

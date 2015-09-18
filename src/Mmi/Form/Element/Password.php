<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Form\Element;

class Password extends ElementAbstract {

	/**
	 * Buduje pole
	 * @return string
	 */
	public function fetchField() {
		$html = '<input ';
		$html .= 'type="password" ' . $this->_getHtmlOptions() . '/>';
		return $html;
	}

}

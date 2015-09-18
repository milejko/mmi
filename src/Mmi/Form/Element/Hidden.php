<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Form\Element;

class Hidden extends ElementAbstract {

	public function init() {
		$this->setRenderingOrder(['fetchField', 'fetchErrors']);
	}

	/**
	 * Buduje pole
	 * @return string
	 */
	public function fetchField() {
		if (isset($this->_options['value'])) {
			$this->_options['value'] = str_replace('"', '&quot;', $this->_options['value']);
		}
		$html = '<input ';
		$html .= 'type="hidden" ' . $this->_getHtmlOptions() . '/>';
		return $html;
	}

}

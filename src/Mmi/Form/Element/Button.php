<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Form\Element;

class Button extends ElementAbstract {

	/**
	 * Ignorowanie tego pola
	 */
	public function init() {
		$this->setIgnore();
		$this->setRenderingOrder(['fetchField', 'fetchErrors', 'fetchCustomHtml']);
	}

	/**
	 * Buduje pole
	 * @return string
	 */
	public function fetchField() {
		$html = '<input ';
		if (isset($this->_options['label'])) {
			$this->_options['value'] = $this->_options['label'];
		}
		$html .= 'type="button" ' . $this->_getHtmlOptions() . '/>';
		return $html;
	}

}

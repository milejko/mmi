<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Form\Element;

class Text extends ElementAbstract {

	public function fetchField() {
		$this->addFilter('Input');
		if (isset($this->_options['value'])) {
			$filter = $this->_getFilter('input');
			$this->_options['value'] = $filter->filter($this->_options['value']);
		}
		if (isset($this->_options['placeholder']) && $this->_translatorEnabled && ($this->getTranslate() !== null)) {
			$this->_options['placeholder'] = $this->getTranslate()->_($this->_options['placeholder']);
		}
		$html = '<input ';
		$html .= 'type="text" ' . $this->_getHtmlOptions() . '/>';
		return $html;
	}

}

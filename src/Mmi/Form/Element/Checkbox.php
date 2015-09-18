<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Form\Element;

class Checkbox extends ElementAbstract {

	/**
	 * Kolejność renderowania pola
	 * @var array
	 */
	protected $_renderingOrder = [
		'fetchField', 'fetchLabel', 'fetchDescription', 'fetchErrors'
	];

	public function fetchLabel() {
		$this->_options['labelPostfix'] = '';
		return parent::fetchLabel();
	}

	public function fetchField() {
		if (isset($this->_options['value']) && $this->_options['value'] == 1) {
			$this->_options['checked'] = 'checked';
		} else {
			unset($this->_options['checked']);
		}
		$this->_options['value'] = '1';
		return '<input type="checkbox" ' . $this->_getHtmlOptions() . '/>';
	}

}

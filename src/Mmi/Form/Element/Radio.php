<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Form\Element;

class Radio extends ElementAbstract {

	/**
	 * Buduje pole
	 * @return string
	 */
	public function fetchField() {
		$baseId = $this->getId();
		$html = '<ul id="' . $this->getId() . '-list">';
		foreach ($this->getMultiOptions() as $key => $caption) {
			$this->unserOption('checked');
			if ($this->getValue() !== null && $this->getValue() == $key) {
				$this->setOption('checked', '');
			}
			$liClass = '';
			//wartość wyłączona
			if (strpos($key, ':disabled') !== false) {
				$this->setDisabled();
			}
			if (mb_stripos($key, ':disabled')) {
				$key = mb_strstr($key, ':disabled', true, 'utf-8');
				$this->_options['disabled'] = '';
				$this->_options['value'] = '';
				$liClass = 'disabled';
			} else {
				unset($this->_options['disabled']);
				$this->_options['value'] = $key;
			}
			$f = new \Mmi\Filter\Url();
			$currentId = $this->getId() . '-' . $f->filter($key);
			$html .= '<li id="' . $currentId . '-item" class="' . $liClass . '">
				<input type="radio" value="' . $key . '" />
				<label for="' . $currentId . '">' . $caption . '</label>
			</li>';
		}
		$html .= '</ul>';
		return $html;
	}
	
	/**
	 * Buduje etykietę pola
	 * @return string
	 */
	public function fetchLabel() {
		//brak labelki
		if (!isset($this->_options['label'])) {
			return;
		}
		$requiredClass = '';
		$required = '';
		//html znaku wymagania
		if ($this->getRequired()) {
			$requiredClass = ' class="required"';
			$required = '<span class="required">' . $this->getOption('data-requiredAsterisk') . '</span>';
		}
		//tłumaczenie labelki
		$label = $this->_options['label'];
		if ($this->_translatorEnabled) {
			$label = $this->getTranslate()->_($label);
		}
		//rendering
		return '<label' . $requiredClass . '>' . $label . $this->_options['labelPostfix'] . $required . '</label>';
	}

}

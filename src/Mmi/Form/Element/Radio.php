<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Form\Element;

class Radio extends ElementAbstract {

	/**
	 * Ustawia klasy dla poszczególnych labelek
	 * @param array $class - tablica $key => $class
	 * @return \Mmi\Form\Element\Radio
	 */
	public function setLabelClass(array $class) {
		$this->_options['labelClass'] = $class;
		return $this;
	}

	/**
	 * Buduje pole
	 * @return string
	 */
	public function fetchField() {
		$baseId = $this->getOption('id');
		$labelClass = isset($this->_options['labelClass']) ? $this->_options['labelClass'] : [];
		$value = $this->getValue();
		$this->unsetOption('value');
		$html = '<ul id="' . $this->id . '_list">';
		
		foreach ($this->getMultiOptions() as $key => $caption) {
			unset($this->_options['checked']);
			if ($value == $key && !is_null($value)) {
				$this->_options['checked'] = 'checked';
			}
			$liClass = '';
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
			$this->_options['id'] = $baseId . '_' . $f->filter($key);
			$classTag = "";
			foreach ($labelClass as $labelId => $className) {
				if ($labelId == $key) {
					$classTag .= 'class="' . $className . '" ';
				}
			}
			$html .= '<li id="' . $this->_options['id'] . '_item" class="' . $liClass . '">
				<input type="radio" ' . $this->_getHtmlOptions() . '/>
				<label ' . $classTag . 'for="' . $this->_options['id'] . '">' . $caption . '</label>
			</li>';
		}
		$html .= '</ul>';
		$this->_options['id'] = $baseId;
		$this->setValue($value);
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
		//html znaku wymagania
		if (isset($this->_options['required']) && $this->_options['required'] && isset($this->_options['markRequired']) && $this->_options['markRequired']) {
			$requiredClass = ' class="required"';
			$required = '<span class="required">' . $this->_requiredAsterisk . '</span>';
		} else {
			$requiredClass = '';
			$required = '';
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

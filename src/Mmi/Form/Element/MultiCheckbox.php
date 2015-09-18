<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Form\Element;

class MultiCheckbox extends ElementAbstract {

	/**
	 * Ustawia klasy dla poszczególnych labelek
	 * @param array $class - tablica $key => $class
	 * @return \Mmi\Form\Element\MultiCheckbox
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
		$baseId = $this->_options['id'];
		$multiOptions = isset($this->_options['multiOptions']) ? $this->_options['multiOptions'] : [];
		$labelClass = isset($this->_options['labelClass']) ? $this->_options['labelClass'] : [];
		$values = isset($this->_options['value']) ? $this->_options['value'] : null;
		unset($this->_options['value']);
		$html = '<ul id="' . $this->getOption('id') . '_list">';
		$this->_options['name'] = $this->_options['name'] . '[]';
		foreach ($multiOptions as $key => $caption) {
			unset($this->_options['checked']);
			if (!is_array($values)) {
				$values = [$values];
			}
			if (!empty($values) && in_array($key, $values)) {
				$this->_options['checked'] = 'checked';
			}
			$f = new \Mmi\Filter\Url();
			$this->_options['id'] = $baseId . '_' . $f->filter($key);
			$this->_options['value'] = $key;

			$classTag = "";
			foreach ($labelClass as $labelId => $className) {
				if ($labelId == $key) {
					$classTag .= 'class="' . $className . '" ';
				}
			}

			if (strpos($key, ':divide') !== false) {
				$html .= '<li class="divide"></li>';
			} elseif (strpos($key, ':disabled') !== false) {
				$this->_options['value'] = '';
				$this->_options['disabled'] = 'disabled';
				$html .= '<li class="disabled" id="' . $this->_options['id'] . '_item' . '">
					<input type="checkbox" ' . $this->_getHtmlOptions() . '/>
					<label ' . $classTag . 'for="' . $this->_options['id'] . '">' . $caption . '</label>
				</li>';
			} else {
				unset($this->_options['disabled']);
				$html .= '<li id="' . $this->_options['id'] . '_item' . '">
					<input type="checkbox" ' . $this->_getHtmlOptions() . '/>
					<label ' . $classTag . 'for="' . $this->_options['id'] . '">' . $caption . '</label>
				</li>';
			}
		}
		$html .= '</ul>';
		$this->_options['id'] = $baseId;
		return $html;
	}

	/**
	 * Buduje etykietę pola
	 * @return string
	 */
	public function fetchLabel() {
		if (!$this->getOption('label')) {
			return;
		}
		$requiredClass = '';
		$required = '';
		//oznaczenie wymagania pola
		if ($this->getOption('required') && $this->getOption('markRequired')) {
			$requiredClass = ' class="required"';
			$required = '<span class="required">' . $this->getOption('requiredAsterisk') . '</span>';
		}
		$label = $this->getOption('label');
		//tłumaczenie labelki
		if ($this->_translatorEnabled) {
			$label = $this->getTranslate()->_($this->getOption('label'));
		}
		return '<label' . $requiredClass . '>' . $label . $this->getOption('labelPostfix') . $required . '</label>';
	}

}

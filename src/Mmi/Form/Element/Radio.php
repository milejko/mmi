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
		$value = $this->getValue();
		$html = '<ul id="' . $this->getId() . '-list">';
		$f = new \Mmi\Filter\Url();
		foreach ($this->getMultiOptions() as $key => $caption) {
			//konfiguracja pola
			$this->setValue($key)
				->unsetOption('checked')
				->setId($baseId . '-' . $f->filter($key));
			//ustalenie zaznaczenia
			if ($value !== null && $value == $key) {
				$this->setOption('checked', '');
			}
			//wartość wyłączona
			if (strpos($key, ':disabled') !== false) {
				$this->setDisabled();
			}
			$html .= '<li id="' . $this->getId() . '-item">
				<input type="radio" ' . $this->_getHtmlOptions() . ' />
				<label for="' . $this->getId() . '">' . $caption . '</label></li>';
		}
		//reset całego pola
		$this->setId($baseId)
			->setValue($value);
		return $html . '</ul>';
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

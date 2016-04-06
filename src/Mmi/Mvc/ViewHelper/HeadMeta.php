<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Mvc\ViewHelper;

class HeadMeta extends HeadAbstract {

	/**
	 * Dane
	 * @var array
	 */
	private $_data = [];

	/**
	 * Metoda główna, dodaje właściwość do stosu
	 * @param array $params parametry opisujące pola
	 * @param boolean $prepend dodaj na początek stosu
	 * @param string $conditional warunek np. ie6
	 * @return \Mmi\Mvc\ViewHelper\HeadMeta
	 */
	public function headMeta(array $params = [], $prepend = false, $conditional = '') {
		//jeśli brak parametrów - wyjście
		if (empty($params)) {
			return $this;
		}
		//warunek
		$params['conditional'] = $conditional;
		if (array_search($params, $this->_data) !== false) {
			return '';
		}
		//wstawienie przed lub po
		if ($prepend) {
			array_unshift($this->_data, $params);
		} else {
			array_push($this->_data, $params);
		}
		return '';
	}
	
	/**
	 * Dodaje znacznik dla Open Graph
	 * @param string $property nazwa właściwości, np. og:image
	 * @param string $content zawartość
	 * @param boolean $prepend dodaj na początek stosu
	 * @param string $conditional warunek np. ie6
	 * @return \Mmi\Mvc\ViewHelper\HeadMeta
	 */
	public function openGraph($property, $content, $prepend = false, $conditional = '') {
		return $this->headMeta(['property' => $property, 'content' => $content], $prepend, $conditional);
	}
	
	/**
	 * Renderer znaczników meta
	 * @return string
	 */
	public function __toString() {
		$html = '';
		foreach ($this->_data as $meta) {
			$conditional = $meta['conditional'];
			unset($meta['conditional']);
			if ($conditional) {
				$html .= '<!--[if ' . $conditional . ']>';
			}
			$html .= '	<meta ';
			foreach ($meta as $key => $value) {
				$html .= $key . '="' . $value . '" ';
			}
			$html .= '/>';
			if ($conditional) {
				$html .= '<![endif]-->';
			}
			$html .= PHP_EOL;
		}
		return $html;
	}

}

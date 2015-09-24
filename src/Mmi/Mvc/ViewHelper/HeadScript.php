<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Mvc\ViewHelper;

class HeadScript extends HeadAbstract {

	/**
	 * Dane
	 * @var array
	 */
	private $_data = [];

	/**
	 * Metoda główna, dodaje skrypt do stosu
	 * @param array $params parametry skryptu
	 * @param boolean $prepend dodaj na początek stosu
	 * @param string $conditional warunek np. ie6
	 * @return \Mmi\Mvc\ViewHelper\HeadScript
	 */
	public function headScript(array $params = [], $prepend = false, $conditional = '') {
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
	 * Renderer skryptów
	 * @return string
	 */
	public function __toString() {
		$html = '';
		//renderowanie kolejnych skryptów
		foreach ($this->_data as $script) {
			if (isset($script['script'])) {
				$scriptContent = $script['script'];
				unset($script['script']);
			}
			//dodawanie klauzuli warunku
			$conditional = $script['conditional'];
			unset($script['conditional']);
			if ($conditional) {
				$html .= '<!--[if ' . $conditional . ']>';
			}
			$html .= '	<script ';
			$crc = $script['crc'];
			unset($script['crc']);
			foreach ($script as $key => $value) {
				if ($key == 'src') {
					if (strpos($value, '?')) {
						$value .= '&crc=' . $crc;
					} else {
						$value .= '?crc=' . $crc;
					}
				}
				$html .= htmlspecialchars($key) . '="' . htmlspecialchars($value) . '" ';
			}
			$html .= '>';
			if (isset($scriptContent)) {
				$html .= PHP_EOL . '		// <![CDATA[' . PHP_EOL . $scriptContent . PHP_EOL . '		// ]]>';
				unset($scriptContent);
			}
			$html .= '</script>';
			if ($conditional) {
				$html .= '<![endif]-->';
			}
			$html .= PHP_EOL;
		}
		return $html;
	}

	/**
	 * Dodaje na koniec stosu skrypt z pliku
	 * @param string $src źródło
	 * @param string $type typ
	 * @param array $params dodatkowe parametry
	 * @param string $conditional warunek np. ie6
	 * @return \Mmi\Mvc\ViewHelper\HeadScript
	 */
	public function appendFile($src, $type = 'text/javascript', array $params = [], $conditional = '') {
		return $this->setFile($src, $type, $params, false, $conditional);
	}

	/**
	 * Dodaje na początek stosu skrypt z pliku
	 * @param string $src źródło
	 * @param string $type typ
	 * @param array $params dodatkowe parametry
	 * @param string $conditional warunek np. ie6
	 * @return \Mmi\Mvc\ViewHelper\HeadScript
	 */
	public function prependFile($src, $type = 'text/javascript', array $params = [], $conditional = '') {
		return $this->setFile($src, $type, $params, true, $conditional);
	}

	/**
	 * Dodaje do stosu skrypt z pliku
	 * @param string $src źródło
	 * @param string $type typ
	 * @param array $params dodatkowe parametry
	 * @param boolean $prepend dodaj na początek stosu
	 * @param string $conditional warunek np. ie6
	 * @return \Mmi\Mvc\ViewHelper\HeadScript
	 */
	public function setFile($src, $type = 'text/javascript', array $params = [], $prepend = false, $conditional = '') {
		return $this->headScript(array_merge($params, ['type' => $type, 'src' => $src, 'crc' => $this->_getCrc($src)]), $prepend, $conditional);
	}

	/**
	 * Dodaje na koniec stosu treść skryptu
	 * @param string $script zawartość skryptu
	 * @param string $type typ
	 * @param array $params dodatkowe parametry
	 * @param string $conditional warunek np. ie6
	 * @return \Mmi\Mvc\ViewHelper\HeadScript
	 */
	public function appendScript($script, $type = 'text/javascript', array $params = [], $conditional = '') {
		return $this->setScript($script, $type, $params, false, $conditional);
	}

	/**
	 * Dodaje na początek stosu treść skryptu
	 * @param string $script zawartość skryptu
	 * @param string $type typ
	 * @param array $params dodatkowe parametry
	 * @param string $conditional warunek np. ie6
	 * @return \Mmi\Mvc\ViewHelper\HeadScript
	 */
	public function prependScript($script, $type = 'text/javascript', array $params = [], $conditional = '') {
		return $this->setScript($script, $type, $params, true, $conditional);
	}

	/**
	 * Dodaje do stosu treść skryptu
	 * @param string $script zawartość skryptu
	 * @param string $type typ
	 * @param array $params dodatkowe parametry
	 * @param boolean $prepend dodaj na początek stosu
	 * @param string $conditional warunek np. ie6
	 * @return \Mmi\Mvc\ViewHelper\HeadScript
	 */
	public function setScript($script, $type = 'text/javascript', array $params = [], $prepend = false, $conditional = '') {
		return $this->headScript(array_merge($params, ['type' => $type, 'script' => $script, 'crc' => 0]), $prepend, $conditional);
	}

}

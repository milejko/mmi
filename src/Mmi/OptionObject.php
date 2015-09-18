<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi;

abstract class OptionObject {

	/**
	 * Opcje pola
	 * @var array
	 */
	protected $_options = [];

	/**
	 * Fabryka obiektów
	 * @return \self
	 */
	public static function factory() {
		return new self();
	}

	/**
	 * Ustawia opcję
	 * @param string $key klucz
	 * @param string $value wartość
	 * @return \Mmi\OptionObject
	 */
	public function setOption($key, $value) {
		$this->_options[$key] = $value;
		return $this;
	}

	/**
	 * Zwraca opcję po kluczu
	 * @param string $key klucz
	 * @return mixed
	 */
	public function getOption($key) {
		return isset($this->_options[$key]) ? $this->_options[$key] : null;
	}

	/**
	 * Usuwa opcję
	 * @param string $key klucz
	 * @return \Mmi\OptionObject
	 */
	public function unsetOption($key) {
		unset($this->_options[$key]);
		return $this;
	}

	/**
	 * Sprawdza istnienie opcji
	 * @param string $key klucz
	 * @return boolean
	 */
	public function issetOption($key) {
		return array_key_exists($key, $this->_options);
	}

	/**
	 * Ustawia wszystkie opcje na podstawie tabeli
	 * @param array $options tabela opcji
	 * @param boolean $reset usuwa poprzednie wartości (domyślnie nie)
	 * @return \Mmi\OptionObject
	 */
	public function setOptions(array $options = [], $reset = false) {
		if ($reset) {
			$this->_options = $options;
		}
		foreach ($options as $key => $value) {
			$this->_options[$key] = $value;
		}
		return $this;
	}

	/**
	 * Zwraca wszystkie opcje
	 * @return array
	 */
	public function getOptions() {
		return $this->_options;
	}

	/**
	 * Obsługa getterów i setterów np. getAddress czy setPort
	 * @param string $name
	 * @param array $params
	 * @return mixed
	 */
	public function __call($name, $params) {
		$matches = [];
		//obsługa getteróœ
		if (preg_match('/get([a-zA-Z0-9]+)/', $name, $matches)) {
			return $this->getOption(lcfirst($matches[1]));
		}
		//obsługa setterów
		if (preg_match('/set([a-zA-Z0-9]+)/', $name, $matches)) {
			return $this->setOption(lcfirst($matches[1]), isset($params[0]) ? $params[0] : null);
		}
	}

}

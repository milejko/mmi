<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Log;

/**
 * Klasa konfiguracji loggera
 * 
 * @method ConfigInstance next()
 * @method ConfigInstance current()
 * @method ConfigInstance rewind()
 */
class Config extends \Mmi\DataObject {

	/**
	 * Indeks elementów
	 * @var integer
	 */
	public $_index = 0;

	/**
	 * Nazwa loggera
	 * @var string
	 */
	public $_name = 'App';

	/**
	 * Dodaje element nawigatora
	 * @param ConfigElement $element
	 * @return \Mmi\Log\Config
	 */
	public function addInstance(ConfigInstance $element) {
		$this->_data[$this->_index++] = $element;
		return $this;
	}

	/**
	 * Zablokowany setter
	 * @param string $key
	 * @param mixed $value
	 * @throws Exception
	 */
	public function __set($key, $value) {
		throw new Exception('Unable to set: {' . $key . '} to value = ' . $value);
	}

	/**
	 * Nazwa loggera
	 * @param string $name
	 * @return \Mmi\Log\Config
	 */
	public function setName($name) {
		$this->_name = $name;
		return $this;
	}

	/**
	 * Pobiera nazwę loggera
	 * @return string
	 */
	public function getName() {
		return $this->_name;
	}

}

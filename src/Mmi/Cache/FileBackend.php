<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Cache;

/**
 * Backend plikowy
 */
class FileBackend implements CacheBackendInterface {

	/**
	 * Przechowuje ścieżkę zapisu
	 * @var string
	 */
	protected $_savePath;

	/**
	 * Kostruktor
	 * @param \Mmi\Cache\CacheConfig $config konfiguracja
	 */
	public function __construct(\Mmi\Cache\CacheConfig $config) {
		$this->_savePath = $config->path;
	}

	/**
	 * Ładuje dane o podanym kluczu
	 * @param string $key klucz
	 */
	public function load($key) {
		if (file_exists($this->_savePath . '/' . $key)) {
			return file_get_contents($this->_savePath . '/' . $key);
		}
	}

	/**
	 * Zapisuje dane pod podanym kluczem
	 * @param string $key klucz
	 * @param string $data
	 * @param int $lifeTime wygaśnięcie danych w buforze (informacja dla bufora)
	 */
	public function save($key, $data, $lifeTime) {
		if (file_put_contents($this->_savePath . '/' . $key, $data) === false) {
			return false;
		}
		return true;
	}

	/**
	 * Kasuje dane o podanym kluczu
	 * @param string $key klucz
	 */
	public function delete($key) {
		if (file_exists($this->_savePath . '/' . $key)) {
			unlink($this->_savePath . '/' . $key);
		}
	}

	/**
	 * Kasuje wszystkie dane
	 */
	public function deleteAll() {
		foreach (glob($this->_savePath . '/*') as $fileName) {
			unlink($fileName);
		}
	}

}

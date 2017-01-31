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
 * Backend bufora w APC
 */
class ApcBackend implements CacheBackendInterface {

	/**
	 * Cache namespace
	 * @var string
	 */
	private $_namespace;

	/**
	 * Kostruktor
	 * @param \Mmi\Cache\CacheConfig $config konfiguracja
	 */
	public function __construct(\Mmi\Cache\CacheConfig $config) {
		$this->_namespace = crc32(BASE_PATH);
	}

	/**
	 * Ładuje dane o podanym kluczu
	 * @param string $key klucz
	 */
	public function load($key) {
		return \apcu_fetch($this->_namespace . '_' . $key);
	}

	/**
	 * Zapisuje dane pod podanym kluczem
	 * @param string $key klucz
	 * @param string $data
	 * @param int $lifeTime wygaśnięcie danych w buforze (informacja dla bufora)
	 */
	public function save($key, $data, $lifeTime) {
		return \apcu_store($this->_namespace . '_' . $key, $data, $lifeTime);
	}

	/**
	 * Kasuje dane o podanym kluczu
	 * @param string $key klucz
	 */
	public function delete($key) {
		return \apcu_delete($this->_namespace . '_' . $key);
	}

	/**
	 * Kasuje wszystkie dane
	 */
	public function deleteAll() {
		return \apcu_clear_cache('user');
	}

}

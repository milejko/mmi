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
 * Backend memcache
 */
class RedisBackend implements CacheBackendInterface {

	/**
	 * Przechowuje obiekt Redisa
	 * @var \Redis
	 */
	private $_server;

	/**
	 * Konfiguracja
	 * @var \Mmi\Cache\CacheConfig
	 */
	private $_config;

	/**
	 * Cache namespace
	 * @var string
	 */
	private $_namespace;

	/**
	 * Ustawia obiekt Memcache
	 * @param \Mmi\Cache\CacheConfig $config konfiguracja
	 */
	public function __construct(\Mmi\Cache\CacheConfig $config, \Mmi\Cache\Cache $cache) {
		$this->_namespace = crc32(BASE_PATH);
		$this->_config = $config;
		$this->_connect();
	}

	/**
	 * Łączenie z serwerem
	 */
	private function _connect() {
		$this->_server = new \Redis;
		$config = parse_url($this->_config->path);
		//format połączenie host/port
		if (isset($config['host']) && isset($config['port'])) {
			//łączenie host/port
			$this->_server->pconnect($config['host'], $config['port']);
			//autoryzacja użytkownik i hasło
			if (isset($config['pass']) && isset($config['user'])) {
				$this->_server->auth($config['user'] . ':' . $config['pass']);
			}
			//autoryzacja sam użytkownik
			if (isset($config['user'])) {
				$this->_server->auth($config['user']);
			}
			//wybór bazy
			$this->_server->select(($config['path'] ? ltrim($config['path'], '/') : '1'));
			return;
		}
		//połączenie po sockecie
		$this->_server->pconnect($config['path']);
		//baza 0
		$this->_server->select(0);
	}

	/**
	 * Ładuje dane o podanym kluczu
	 * @param string $key klucz
	 */
	public function load($key) {
		return $this->_server->get($this->_namespace . '_' . $key);
	}

	/**
	 * Zapisuje dane pod podanym kluczem
	 * @param string $key klucz
	 * @param string $data
	 * @param int $lifeTime wygaśnięcie danych w buforze (informacja dla bufora)
	 * @return boolean
	 */
	public function save($key, $data, $lifeTime) {
		$this->_server->set($this->_namespace . '_' . $key, $data, $lifeTime);
		return true;
	}

	/**
	 * Kasuje dane o podanym kluczu
	 * @param string $key klucz
	 * @return boolean
	 */
	public function delete($key) {
		$this->_server->delete($this->_namespace . '_' . $key);
		return true;
	}

	/**
	 * Kasuje wszystkie dane
	 */
	public function deleteAll() {
		return $this->_server->flushDB();
	}
	
}

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
 * Handler memcache
 */
class MemcacheHandler implements CacheHandlerInterface {

	/**
	 * Przechowuje obiekt Memcache
	 * @var Memcache
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
	 * @param \Mmi\Cache\Cache $cache objekt bufora
	 */
	public function __construct(Cache $cache) {
		$this->_namespace = crc32(BASE_PATH);
		$this->_config = $cache->getConfig();
		$this->_connect();
	}

	/**
	 * Łączenie z pulą serwerów
	 */
	private function _connect() {
		$this->_server = new \Memcache;
		//dodawanie całej puli
		if (is_array($this->_config->path)) {
			$this->_addServers($this->_config->path);
			return;
		}
		//dodawanie pojedynczego serwera
		$this->_addServer($this->_config->path);
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
		if ($lifeTime > 2592000) {
			//memcache bug ta wartość nie może być większa
			$lifeTime = 2592000;
		}
		$this->_server->set($this->_namespace . '_' . $key, $data, 0, $lifeTime);
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
		$this->_server->flush();
	}

	/**
	 * Parsuje adres serwera memcached
	 * @param string $link źródło
	 * @return array
	 */
	private function _parseMemcacheAddress($link) {
		$protoSeparator = strpos($link, '://');
		if ($protoSeparator !== false) {
			$link = substr($link, $protoSeparator + 3);
		}
		$server = $link;
		$serverOptions = [];
		$hookSeparator = strpos($link, '?');
		if ($hookSeparator !== false) {
			$server = substr($link, 0, $hookSeparator);
			parse_str(substr($link, $hookSeparator + 1), $serverOptions);
		}
		$server = explode(':', $server);
		$serverOptions['host'] = $server[0];
		$serverOptions['port'] = $server[1];
		return $serverOptions;
	}

	/**
	 * Dodaje serwer
	 * @param string $server adres serwera
	 */
	private function _addServer($server) {
		$server = $this->_parseMemcacheAddress($server);
		$this->_server->addServer(
			$server['host'], $server['port'], isset($server['persistent']) ? $server['persistent'] : true, isset($server['weight']) ? $server['weight'] : 1, isset($server['timeout']) ? $server['timeout'] : 15, isset($server['retry_interval']) ? $server['retry_interval'] : 15
		);
	}

	/**
	 * Dodaje serwery
	 * @param array $servers tablica adresów serwera
	 */
	private function _addServers(array $servers) {
		foreach ($servers as $server) {
			$this->_addServer($server);
		}
	}

}

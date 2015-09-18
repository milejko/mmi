<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi;

/**
 * Obiekt bufora danych
 */
class Cache {

	/**
	 * Konfiguracja bufora
	 * @var \Mmi\Cache\Config
	 */
	protected $_config;

	/**
	 * Backend bufora
	 * @var \Mmi\Cache\Backend\Interface
	 */
	protected $_backend;

	/**
	 * Przestrzeń nazw dla rejestru
	 * @var string
	 */
	protected $_registryNamespace;

	/**
	 * Konstruktor, wczytuje konfigurację i ustawia backend
	 */
	public function __construct(\Mmi\Cache\Config $config) {
		$this->_config = $config;
		$saveHandler = $config->handler;
		//określanie klasy backendu
		$backendClassName = '\\Mmi\\Cache\\Backend\\' . ucfirst($saveHandler);
		//powoływanie obiektu backendu
		$this->_backend = new $backendClassName($config);
		//namespace w rejestrze
		$this->_registryNamespace = 'Cache-' . crc32($config->path . $config->handler) . '-';
		//niepoprawny backend
		if (!($this->_backend instanceof \Mmi\Cache\Backend\BackendInterface)) {
			throw new \Exception('Cache backend invalid');
		}
	}

	/**
	 * Ładuje (jeśli istnieją) dane z bufora
	 * @param string $key klucz
	 * @return mixed
	 */
	public function load($key) {
		//bufor nieaktywny
		if (!$this->isActive()) {
			return;
		}
		//pobranie z rejestru aplikacji jeśli istnieje
		if (\Mmi\App\Registry::issetVar($this->_registryNamespace . $key)) {
			return \Mmi\App\Registry::getVar($this->_registryNamespace . $key);
		}
		//pobranie z backendu i zapis do rejestru
		return \Mmi\App\Registry::setVar($this->_registryNamespace . $key, $this->_getValidCacheData($this->_backend->load($key)));
	}

	/**
	 * Zapis danych
	 * Dane zostaną zserializowane i zapisane w backendzie
	 * @param mixed $data dane
	 * @param string $key klucz
	 * @param integer $lifetime czas życia
	 */
	public function save($data, $key, $lifetime = null) {
		//bufor nieaktywny
		if (!$this->isActive()) {
			return;
		}
		//brak podanego klucza (użycie domyślnego z cache)
		if (!$lifetime) {
			//jeśli null - użycie domyślnego, jeśli zero lub false to maksymalny
			$lifetime = $lifetime === null ? $this->_config->lifetime : 2505600;
		}
		//dodanie losowej wartości do długości bufora
		$lifetime += rand(0, 15);
		//zapis w rejestrze
		\Mmi\App\Registry::setVar($this->_registryNamespace . $key, $data);
		//zapis w backendzie
		return $this->_backend->save($key, $this->_setCacheData($data, time() + $lifetime), $lifetime);
	}

	/**
	 * Usuwanie danych z bufora na podstawie klucza
	 * @param string $key klucz
	 */
	public function remove($key) {
		//bufor nieaktywny
		if (!$this->isActive()) {
			return;
		}
		//usunięcie z rejestru
		\Mmi\App\Registry::unsetVar($key);
		//usunięcie z backendu
		return $this->_backend->delete($key);
	}

	/**
	 * Usuwa wszystkie dane z bufora
	 * UWAGA: nie usuwa danych z rejestru
	 */
	public function flush() {
		//bufor nieaktywny
		if (!$this->isActive()) {
			return;
		}
		return $this->_backend->deleteAll();
	}

	/**
	 * Zwraca aktywność cache
	 * @return boolean
	 */
	public function isActive() {
		return $this->_config->active;
	}

	/**
	 * Serializuje dane i stempluje datą wygaśnięcia
	 * @param mixed $data dane
	 * @param int $expire wygasa
	 * @return string
	 */
	protected function _setCacheData($data, $expire) {
		return serialize(['expire' => $expire, 'data' => $data]);
	}

	/**
	 * Zwraca aktualne dane (sprawdza ważność)
	 * @param mixed $data dane
	 * @return mixed
	 */
	protected function _getValidCacheData($data) {
		if (!($data = unserialize($data))) {
			return;
		}
		if (!isset($data['expire']) || !isset($data['data'])) {
			return;
		}
		if ($data['expire'] > time()) {
			return $data['data'];
		}
	}

}

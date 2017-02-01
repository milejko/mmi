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
 * Obiekt bufora danych
 */
class Cache {

	/**
	 * Konfiguracja bufora
	 * @var CacheConfig
	 */
	private $_config;

	/**
	 * Backend bufora
	 * @var BackendInterface
	 */
	private $_backend;
	
	/**
	 * Rejestr bufora
	 * @var CacheRegistry
	 */
	private $_registry;

	/**
	 * Konstruktor, wczytuje konfigurację i ustawia backend
	 */
	public function __construct(CacheConfig $config) {
		$this->_config = $config;
		//powoływanie rejestru
		$this->_registry = new CacheRegistry;
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
		if ($this->getRegistry()->issetOption($key)) {
			return $this->getRegistry()->getOption($key);
		}
		//pobranie z backendu zapis do rejestru i zwrot wartości
		return $this->getRegistry()->setOption($key, $this->_getValidCacheData($this->_backend->load($key)))
				->getOption($key);
	}

	/**
	 * Zapis danych
	 * Dane zostaną zserializowane i zapisane w backendzie
	 * @param mixed $data dane
	 * @param string $key klucz
	 * @param integer $lifetime czas życia
	 * @return boolean
	 */
	public function save($data, $key, $lifetime = null) {
		//bufor nieaktywny
		if (!$this->isActive()) {
			return true;
		}
		//brak podanego klucza (użycie domyślnego z cache)
		if (!$lifetime) {
			//jeśli null - użycie domyślnego, jeśli zero lub false to maksymalny
			$lifetime = $lifetime === null ? $this->_config->lifetime : 2505600;
		}
		//dodanie losowej wartości do długości bufora
		$lifetime += rand(0, 15);
		//zapis w rejestrze
		$this->getRegistry()->setOption($key, $data);
		//zapis w backendzie
		return (bool)$this->_backend->save($key, $this->_setCacheData($data, time() + $lifetime), $lifetime);
	}

	/**
	 * Usuwanie danych z bufora na podstawie klucza
	 * @param string $key klucz
	 */
	public function remove($key) {
		//bufor nieaktywny
		if (!$this->isActive()) {
			return true;
		}
		//usunięcie z rejestru
		$this->getRegistry()->unsetOption($key);
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
		//czyszczenie rejestru
		$this->getRegistry()->setOptions([]);
		//czyszczenie backendu
		return $this->_backend->deleteAll();
	}

	/**
	 * Zwraca aktywność cache
	 * @return boolean
	 */
	public function isActive() {
		//sprawdzenie aktywności
		if (!$this->_config->active) {
			return false;
		}
		//ustawienie backendu
		$this->_setupBackend();
		return true;
	}
	
	/**
	 * Zwraca rejestr bufora
	 * @return CacheRegistry
	 */
	public function getRegistry() {
		return $this->_registry;
	}

	/**
	 * Serializuje dane i stempluje datą wygaśnięcia
	 * @param mixed $data dane
	 * @param int $expire wygasa
	 * @return string
	 */
	protected function _setCacheData($data, $expire) {
		return serialize(['e' => $expire, 'd' => $data]);
	}

	/**
	 * Ustawia backend bufora
	 * @param \Mmi\Cache\CacheBackendInterface $backend
	 */
	protected function _setBackend(CacheBackendInterface $backend) {
		$this->_backend = $backend;
	}

	/**
	 * Zwraca aktualne dane (sprawdza ważność)
	 * @param mixed $data dane
	 * @return mixed
	 */
	protected function _getValidCacheData($data) {
		//brak danych
		if (!($data = unserialize($data))) {
			return;
		}
		//dane niepoprawne
		if (!isset($data['e']) || !isset($data['d'])) {
			return;
		}
		//dane wygasłe
		if ($data['e'] > time()) {
			return $data['d'];
		}
	}

	/**
	 * Ustawianie backendu
	 * @throws CacheException
	 */
	protected function _setupBackend() {
		//backend już ustawiony
		if (null !== $this->_backend) {
			return;
		}
		//określanie klasy backendu
		$backendClassName = '\\Mmi\\Cache\\' . ucfirst($this->_config->handler) . 'Backend';
		try {
			//powoływanie obiektu backendu
			$this->_setBackend(new $backendClassName($this->_config, $this));
		} catch (\Exception $e) {
			\Mmi\App\FrontController::getInstance()->getLogger()->addWarning('Cache backend could not be initialized, DummyBackend used instead ' . $e->getMessage());
			$this->_setBackend(new DummyBackend($this->_config, $this));
		}
	}

}

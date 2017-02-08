<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Cache;

use \Mmi\Orm;

/**
 * Bazodanowy backend bufora
 */
class DbBackend implements CacheBackendInterface {
	
	/**
	 * Obiekt bufora
	 * @var \Mmi\Cache\Cache
	 */
	private $_cache;
	
	/**
	 * Prefiks kluczy systemowych
	 * @var array
	 */
	CONST SYSTEM_CACHE_PREFIX = 'mmi-';
	
	/**
	 * Prefiks bufora pośredniego
	 */
	CONST REGISTRY_PREFIX = 'intermediate-db-cache';

	/**
	 * Kostruktor
	 * @param \Mmi\Cache\CacheConfig $config konfiguracja
	 */
	public function __construct(\Mmi\Cache\CacheConfig $config, \Mmi\Cache\Cache $cache) {
		$this->_cache = $cache;
		//bufor systemowy już zarejestrowany
		if ($cache->getRegistry()->issetOption($intermediateKey = self::REGISTRY_PREFIX . '-init')) {
			return;
		}
		//nowe zapytanie
		$systemCacheQuery = (new Orm\CacheQuery)
			->whereId()->like(self::SYSTEM_CACHE_PREFIX . '%');
		//iteracja po kolekcji bufora systemowego
		foreach ($systemCacheQuery->find() as $cacheRecord) {
			//ustawianie bufora pośredniego w rejestrze
			$cache->getRegistry()->setOption(self::REGISTRY_PREFIX . $cacheRecord->id, json_decode($cacheRecord->data));
		}
		//informacja o wstępnej inicjalizacji bufora
		$cache->getRegistry()->setOption($intermediateKey, true);
	}

	/**
	 * Ładuje dane o podanym kluczu
	 * @param string $key klucz
	 */
	public function load($key) {
		//sprawdzanie w buforze pośrednim
		if ($this->_cache->getRegistry()->issetOption(self::REGISTRY_PREFIX . $key)) {
			//zwrot z bufora pośredniego
			return $this->_cache->getRegistry()->getOption(self::REGISTRY_PREFIX . $key);
		}
		//wyszukiwanie rekordu
		if (null === $cacheRecord = (new Orm\CacheQuery)->findPk($key)) {
			return;
		}
		//zwrot danych
		return json_decode($cacheRecord->data);
	}

	/**
	 * Zapisuje dane pod podanym kluczem
	 * @param string $key klucz
	 * @param string $data
	 * @param int $lifeTime wygaśnięcie danych w buforze (informacja dla bufora)
	 */
	public function save($key, $data, $lifeTime) {
		//start transakcji
		Orm\DbConnector::getAdapter()->beginTransaction();
		//wyszukiwanie rekordu
		if (null === $cacheRecord = (new Orm\CacheQuery)->findPk($key)) {
			//tworzenie nowego rekordu
			$cacheRecord = new Orm\CacheRecord;
			$cacheRecord->id = $key;
		}
		$cacheRecord->data = json_encode($data);
		//zapis rekordu
		$result = $cacheRecord->save();
		//commit
		Orm\DbConnector::getAdapter()->commit();
		return $result;
	}

	/**
	 * Kasuje dane o podanym kluczu
	 * @param string $key klucz
	 * @return boolean
	 */
	public function delete($key) {
		//wyszukiwanie rekordu
		if (null === $cacheRecord = (new Orm\CacheQuery)->findPk($key)) {
			return true;
		}
		//usuwanie rekordu
		$cacheRecord->delete();
		return true;
	}

	/**
	 * Kasuje wszystkie dane
	 */
	public function deleteAll() {
		//uproszczone usuwanie - jedynm zapytaniem
		\Mmi\Orm\DbConnector::getAdapter()->delete((new Orm\CacheQuery)->getTableName());
	}

}

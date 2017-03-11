<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Cache;

/**
 * Handler bufora w APC
 */
abstract class DistributedCacheHandlerAbstract implements CacheHandlerInterface {

	/**
	 * Cache namespace
	 * @var string
	 */
	protected $_namespace;

	/**
	 * Obiekt bufora
	 * @var Cache
	 */
	protected $_cache;

	/**
	 * Obiekt bufora
	 * @var \Mmi\Cache\Cache
	 */
	protected $_distributedCache;

	/**
	 * Prefiks bufora rozproszonego
	 */
	CONST DEL_PREFIX = 'mmi-cdel-';
	
	/**
	 * Klucz zbuforowanego rozproszonego bufora
	 */
	CONST STORAGE_KEY = 'mmi-cache-distributed-storage';
	
	/**
	 * Kostruktor
	 * @param \Mmi\Cache\Cache $cache obiekt bufora
	 */
	public function __construct(Cache $cache) {
		//namespace
		$this->_namespace = BASE_PATH;
		//przypisanie obiektu bufora
		$this->_cache = $cache;
		//bufor nierozproszony
		if (!$cache->getConfig()->distributed || !$cache->getConfig()->active) {
			return;
		}
		//ustawienie bufora rozproszonego 
		$this->_distributedCache = $this->_getDistributedCache($cache);
	}
	
	/**
	 * Ustawienie bufora rozproszonego
	 * @param \Mmi\Cache\Cache $cache
	 * @return \Mmi\Cache\Cache
	 */
	protected function _getDistributedCache(Cache $cache) {
		//konfiguracja bufora rozproszonego
		$config = new CacheConfig;
		$config->lifetime = $cache->getConfig()->lifetime;
		$config->handler = 'db';
		//zapis lokalnym buforze
		return new Cache($config);
	}

	/**
	 * Sprawdza czy klucz powinien zostać usunięty (rozgłoszony w buforze rozproszonym)
	 * @param string $key klucz
	 * @return boolean
	 */
	protected function _keyShouldBeDeleted($key) {
		//brak rozproszonego bufora
		if (!$this->_distributedCache) {
			return false;
		}
		//brak bufora do usunięcia (pobranie ze zdalnego bufora)
		if (null === $remoteTime = $this->_distributedCache->load($cacheKey = self::DEL_PREFIX . $key)) {
			return false;
		}
		//jeśli czas lokalnego usunięcia jest wyższy niż zdalnego, nie ma potrzeby usuwania
		if ($this->_cache->load($cacheKey) >= $remoteTime) {
			//zapis lokalnie informacji o usunięciu
			return false;
		}
		//zapisujemy czas usunięcia
		$this->_cache->save(time(), $cacheKey);
		return true;
	}

	/**
	 * Ustawia w buforze rozproszonym rozgłoszenie o skasowaniu klucza
	 * @param string $key klucz
	 */
	protected function _broadcastDelete($key) {
		//brak rozproszonego bufora
		if (!$this->_distributedCache) {
			return;
		}
		//rozgłoszenie informacji o usunięciu klucza do bufora Db
		$this->_distributedCache->save($time = time(), $cacheKey = self::DEL_PREFIX . $key);
		//lokalnie już usunięte - zapis do bufora
		$this->_cache->save($time, $cacheKey, 0);
	}

}

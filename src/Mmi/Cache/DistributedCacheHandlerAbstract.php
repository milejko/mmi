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
	 * Prefiks bufora dystrybuowanego
	 */
	CONST DISTRIBUTED_PREFIX = 'mmi-cache-delete-';

	/**
	 * Kostruktor
	 * @param \Mmi\Cache\Cache $cache obiekt bufora
	 */
	public function __construct(\Mmi\Cache\Cache $cache) {
		//namespace
		$this->_namespace = crc32(BASE_PATH) . '-';
		//przypisanie obiektu bufora
		$this->_cache = $cache;
		//bufor nierozproszony
		if (!$cache->getConfig()->distributed || !$cache->getConfig()->active) {
			return;
		}
		//konfiguracja bufora rozproszonego
		$distributedCache = new CacheConfig();
		$distributedCache->lifetime = $cache->getConfig()->lifetime;
		$distributedCache->active = true;
		$distributedCache->handler = 'db';
		//tworzenie klasy bufora rozproszonego
		$this->_distributedCache = new Cache($distributedCache);
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
		if (null === $remoteTime = $this->_distributedCache->load($cacheKey = self::DISTRIBUTED_PREFIX . $key)) {
			return false;
		}
		//pobranie z lokalnego bufora
		$localTime = $this->_cache->load($cacheKey);
		//jeśli czas lokalnego usunięcia jest wyższy niż zdalnego, nie ma potrzeby usuwania
		if ($localTime >= $remoteTime) {
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
		$this->_distributedCache->save($time = time(), $cacheKey = self::DISTRIBUTED_PREFIX . $key);
		//lokalnie już usunięte - zapis do bufora
		$this->_cache->save($time, $cacheKey, 0);
	}

}

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
	 * Obiekt bufora lokalnego (nie rozproszonego)
	 * @var Cache
	 */
	protected $_undistributedCache;

	/**
	 * Obiekt bufora
	 * @var \Mmi\Cache\DistributedStorage
	 */
	protected $_distributedStorage;

	/**
	 * Prefiks bufora rozproszonego
	 */
	CONST DEL_PREFIX = 'mmi-keydel-';

	/**
	 * Wiadomość o usunięciu całego bufora
	 */
	CONST FLUSH_MESSAGE = 'flush-all';

	/**
	 * Klucz zbuforowanego rozproszonego bufora
	 */
	CONST STORAGE_KEY = 'mmi-cache-distributed-storage';

	/**
	 * Czas odświeżania bufora rozproszonego
	 */
	CONST DISTRIBUTED_REFRESH_INTERVAL = 2;

	/**
	 * Usunięcie bufora bez rozgłoszenia
	 */
	abstract protected function _deleteAllNoBroadcasting();

	/**
	 * Kostruktor
	 * @param \Mmi\Cache\Cache $cache obiekt bufora
	 */
	public final function __construct(Cache $cache) {
		//namespace
		$this->_namespace = BASE_PATH;
		//przypisanie obiektu bufora
		$this->_cache = $cache;
		//bufor nierozproszony
		if (!$cache->getConfig()->distributed || !$cache->getConfig()->active) {
			return;
		}
		//bufor aktywny i rozproszony - inicjalizacja
		$this->_initDistributedStorage($cache);
	}

	/**
	 * Kasuje wszystkie dane
	 */
	public final function deleteAll() {
		//usuwa bufor
		$this->_deleteAllNoBroadcasting();
		//rozgłoszenie usunięcia bufora
		$this->_broadcastDeleteAll();
	}

	/**
	 * Inicjalizacja bufora rozproszonego
	 * @param \Mmi\Cache\Cache $cache
	 */
	protected final function _initDistributedStorage(Cache $cache) {
		//klonowanie konfiguracji bufora 
		$cacheConfigClone = clone $cache->getConfig();
		$cacheConfigClone->distributed = false;
		//nowy obiekt bufora lokalnego
		$this->_undistributedCache = new Cache($cacheConfigClone);
		//ustawienie bufora rozproszonego 
		$this->_distributedStorage = $this->_getDistributedStorage();
		//jest informacja o czyszczeniu bufora
		if ($this->_keyShouldBeDeleted(self::FLUSH_MESSAGE)) {
			//wymuszenie czyszczenia bufora (bez rozgłaszania)
			$this->_deleteAllNoBroadcasting();
			//zapis lokalnie informacji o usunięciu
			$this->_undistributedCache->save(time(), self::DEL_PREFIX . self::FLUSH_MESSAGE, 0);
		}
	}

	/**
	 * Ustawienie bufora rozproszonego
	 * @return \Mmi\Cache\Cache
	 */
	protected final function _getDistributedStorage() {
		if (null === $distributedStorage = $this->_undistributedCache->load(self::STORAGE_KEY)) {
			$this->_undistributedCache->save($distributedStorage = new DistributedStorage(), self::STORAGE_KEY, self::DISTRIBUTED_REFRESH_INTERVAL);
		}
		//zapis lokalnym buforze
		return $distributedStorage;
	}

	/**
	 * Sprawdza czy klucz powinien zostać usunięty (rozgłoszony w buforze rozproszonym)
	 * @param string $key klucz
	 * @return boolean
	 */
	protected final function _keyShouldBeDeleted($key) {
		//brak rozproszonego bufora
		if (!$this->_distributedStorage) {
			return false;
		}
		//brak bufora do usunięcia (pobranie ze zdalnego bufora)
		if (null === $remoteTime = $this->_distributedStorage->load($cacheKey = self::DEL_PREFIX . $key)) {
			return false;
		}
		//jeśli czas lokalnego usunięcia jest wyższy niż zdalnego, nie ma potrzeby usuwania
		if ($this->_undistributedCache->load($cacheKey) >= $remoteTime) {
			return false;
		}
		//zapis lokalnie informacji o usunięciu
		$this->_undistributedCache->save(time(), $cacheKey, 0);
		return true;
	}

	/**
	 * Rozgłoszenie o skasowaniu klucza
	 * @param string $key klucz
	 */
	protected final function _broadcastDelete($key) {
		//brak rozproszonego bufora
		if (!$this->_distributedStorage) {
			return;
		}
		//rozgłoszenie informacji o usunięciu klucza do bufora Db
		$this->_distributedStorage->save($time = time(), $cacheKey = self::DEL_PREFIX . $key);
		//lokalnie już usunięte - zapis do bufora
		$this->_undistributedCache->save($time, $cacheKey, 0);
	}

	/**
	 * Rozgłoszenie o usunięciu bufora
	 */
	protected final function _broadcastDeleteAll() {
		//brak rozproszonego bufora, lub flush został już rozgłoszony
		if (!$this->_distributedStorage) {
			return;
		}
		//rozgłoszenie informacji o usunięciu bufora
		$this->_distributedStorage->save($time = time(), $cacheKey = self::DEL_PREFIX . self::FLUSH_MESSAGE);
		//lokalnie już wyczyszczony - zapis do bufora
		$this->_undistributedCache->save($time, $cacheKey, 0);
	}

}

<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Cache;

use Mmi\Cache\Cache;

/**
 * Handler bufora w APC
 */
abstract class DistributedCacheHandlerAbstract implements CacheHandlerInterface
{

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
     * Usunięcie klucza bez rozgłoszenia
     * @return boolean
     */
    abstract protected function _deleteNoBroadcasting($key);

    /**
     * Usunięcie bufora bez rozgłoszenia
     */
    abstract protected function _deleteAllNoBroadcasting();

    /**
     * Kostruktor
     * @param Cache $cache obiekt bufora
     * @throws CacheException
     */
    public final function __construct(Cache $cache)
    {
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
     * Kasuje dane o podanym kluczu
     * @param string $key klucz
     * @return boolean
     * @throws CacheException
     */
    public final function delete($key)
    {
        //usunięcie klucza i rozgłoszenie informacji o usunięciu klucza do bufora Db
        return $this->_deleteNoBroadcasting($key) && $this->_broadcastDelete($key);
    }

    /**
     * Kasuje wszystkie dane
     */
    public final function deleteAll()
    {
        //usuwa bufor
        $this->_deleteAllNoBroadcasting();
        //rozgłoszenie usunięcia bufora
        $this->_broadcastDeleteAll();
    }

    /**
     * Inicjalizacja bufora rozproszonego
     * @param Cache $cache
     * @throws CacheException
     */
    protected final function _initDistributedStorage(Cache $cache)
    {
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
        //czyszczenie pojedynczych kluczy
        foreach ($this->_distributedStorage->getOptions() as $key => $timestamp) {
            //jeśli klucz powinien zostać usunięty usuwa bez dalszego rozgłaszania
            $this->_keyShouldBeDeleted($key) &&
            $this->_deleteNoBroadcasting($key);
        }
    }

    /**
     * Ustawienie bufora rozproszonego
     * @return Cache
     * @throws CacheException
     */
    protected final function _getDistributedStorage()
    {
        //ładowanie rozproszonego bufora z bufora lokalnego
        if (null === $distributedStorage = $this->_undistributedCache->load(self::STORAGE_KEY)) {
            //zapis z krótkim, zdefiniowanym odświeżaniem
            $this->_undistributedCache->save($distributedStorage = new DistributedStorage(), self::STORAGE_KEY, self::DISTRIBUTED_REFRESH_INTERVAL);
        }
        //zapis lokalnym buforze
        return $distributedStorage;
    }

    /**
     * Sprawdza czy klucz powinien zostać usunięty (rozgłoszony w buforze rozproszonym)
     * @param string $key klucz
     * @return boolean
     * @throws CacheException
     */
    protected final function _keyShouldBeDeleted($key)
    {
        //brak informacji o usunięciu klucza
        if (null === $remoteTime = $this->_distributedStorage->getOption($key)) {
            return false;
        }
        //jeśli czas lokalnego usunięcia jest wyższy niż zdalnego, nie wolno usuwać
        if ($this->_undistributedCache->load($cacheKey = self::DEL_PREFIX . $key) >= $remoteTime) {
            return false;
        }
        //zapis lokalnie informacji o czasie usunięcia
        $this->_undistributedCache->save(time(), $cacheKey, 0);
        return true;
    }

    /**
     * Rozgłoszenie o skasowaniu klucza
     * @param string $key klucz
     * @return boolean
     * @throws CacheException
     */
    protected final function _broadcastDelete($key)
    {
        //brak rozproszonego bufora
        if (!$this->_distributedStorage) {
            return true;
        }
        //rozgłoszenie informacji o usunięciu klucza do bufora Db i zapis o lokalnym usunięciu
        return $this->_distributedStorage->save($time = time(), $key) &&
            $this->_undistributedCache->save($time, self::DEL_PREFIX . $key, 0);
    }

    /**
     * Rozgłoszenie o usunięciu bufora
     * @return boolean
     * @throws CacheException
     */
    protected final function _broadcastDeleteAll()
    {
        //brak rozproszonego bufora, lub flush został już rozgłoszony
        if (!$this->_distributedStorage) {
            return true;
        }
        //rozgłoszenie informacji o usunięciu bufora i zapis o lokalnym czyszczeniu
        $this->_distributedStorage->save($time = time(), self::FLUSH_MESSAGE) &&
        $this->_undistributedCache->save($time, self::DEL_PREFIX . self::FLUSH_MESSAGE, 0);
    }

}

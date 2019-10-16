<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Cache;

/**
 * Handler memcache
 */
class RedisHandler implements CacheHandlerInterface
{

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
     * @param \Mmi\Cache\Cache $cache obiekt bufora
     */
    public function __construct(Cache $cache)
    {
        $this->_namespace = BASE_PATH;
        $this->_config = $cache->getConfig();
        $this->_connect();
    }

    /**
     * Łączenie z serwerem
     */
    private function _connect()
    {
        //powoływanie serwera
        $this->_server = new \Redis;
        //parsowanie konfiguracji
        $config = parse_url($this->_config->path);
        //format połączenie host/port
        if (!isset($config['host']) || !isset($config['port'])) {
            //błąd konfiguracji
            throw new CacheException('Configuration path invalid');
        }
        //łączenie host/port
        $this->_server->pconnect($config['host'], $config['port']);
        //autoryzacja
        if (isset($config['user'])) {
            $this->_server->auth($config['user'] . (isset($config['pass']) ? ':' . $config['pass'] : ''));
        }
        //wybór bazy
        $this->_server->select((isset($config['path']) ? ltrim($config['path'], '/') : '1'));
    }

    /**
     * Ładuje dane o podanym kluczu
     * @param string $key klucz
     */
    public function load($key)
    {
        return $this->_server->get($this->_namespace . '_' . $key);
    }

    /**
     * Zapisuje dane pod podanym kluczem
     * @param string $key klucz
     * @param string $data
     * @param int $lifeTime wygaśnięcie danych w buforze (informacja dla bufora)
     * @return boolean
     */
    public function save($key, $data, $lifeTime)
    {
        $this->_server->set($this->_namespace . '_' . $key, $data);
        return true;
    }

    /**
     * Kasuje dane o podanym kluczu
     * @param string $key klucz
     * @return boolean
     */
    public function delete($key)
    {
        $this->_server->del($this->_namespace . '_' . $key);
        return true;
    }

    /**
     * Kasuje wszystkie dane
     */
    public function deleteAll()
    {
        $this->_server->flushDB();
    }

}

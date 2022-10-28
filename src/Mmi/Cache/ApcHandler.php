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
 * Handler bufora w APC
 */
class ApcHandler implements CacheHandlerInterface
{
    private string $_namespace;

    /**
     * Konfiguruje handler
     */
    public function __construct()
    {
        $this->_namespace = md5(__FILE__);
    }

    /**
     * Ładuje dane o podanym kluczu
     * @param string $key klucz
     * @return mixed
     */
    public function load($key)
    {
        //pobranie danych z APC
        return \apcu_fetch($this->_namespace . $key);
    }

    /**
     * Zapisuje dane pod podanym kluczem
     * @param string $key klucz
     * @param string $data
     * @param int $lifeTime wygaśnięcie danych w buforze (informacja dla bufora)
     * @return bool
     */
    public function save($key, $data, $lifeTime)
    {
        //zapis w APC
        \apcu_store($this->_namespace . $key, $data, $lifeTime);
        return true;
    }

    /**
     * Kasuje dane o podanym kluczu
     * @param string $key klucz
     * @return boolean
     */
    public function delete($key)
    {
        //usunięcie z apc
        \apcu_delete($this->_namespace . $key);
        return true;
    }

    /**
     * Kasuje wszystkie dane
     */
    public function deleteAll()
    {
        //czyszczenie bufora apc
        \apcu_clear_cache();
    }
}

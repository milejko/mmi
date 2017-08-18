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
class ApcHandler extends DistributedCacheHandlerAbstract
{

    /**
     * Ładuje dane o podanym kluczu
     * @param string $key klucz
     */
    public function load($key)
    {
        //rozgłoszenie informacji o usunięciu klucza do bufora Db
        return \apcu_fetch($this->_namespace . $key);
    }

    /**
     * Zapisuje dane pod podanym kluczem
     * @param string $key klucz
     * @param string $data
     * @param int $lifeTime wygaśnięcie danych w buforze (informacja dla bufora)
     * @param boolean
     */
    public function save($key, $data, $lifeTime)
    {
        //zapis w APC
        \apcu_store($this->_namespace . $key, $data, $lifeTime);
        return true;
    }

    /**
     * Kasuje klucz bez rozgłaszania
     * @param strings $key klucz
     * @return boolean
     */
    protected function _deleteNoBroadcasting($key)
    {
        //usunięcie z apc
        \apcu_delete($this->_namespace . $key);
        return true;
    }

    /**
     * Kasuje bufor bez rozgłaszania
     */
    protected function _deleteAllNoBroadcasting()
    {
        //czyszczenie bufora apc
        \apcu_clear_cache();
    }

}

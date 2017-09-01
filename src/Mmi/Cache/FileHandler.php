<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Cache;

use \Mmi\App\KernelException;

/**
 * Plikowy backend bufora
 */
class FileHandler extends DistributedCacheHandlerAbstract
{

    /**
     * Ładuje dane o podanym kluczu
     * @param string $key klucz
     */
    public function load($key)
    {
        //próba odczytu pliku
        try {
            return file_get_contents($this->_cache->getConfig()->path . '/' . $key);
        } catch (KernelException $e) {
            //brak akcji
        }
    }

    /**
     * Zapisuje dane pod podanym kluczem
     * @param string $key klucz
     * @param string $data
     * @param int $lifeTime wygaśnięcie danych w buforze (informacja dla bufora)
     */
    public function save($key, $data, $lifeTime)
    {
        //$lifeTime nie jest używany w tym backendzie
        //zapis pliku
        file_put_contents($this->_cache->getConfig()->path . '/' . $key, $data);
        return true;
    }

    /**
     * Kasuje dane o podanym kluczu
     * @param string $key klucz
     * @return boolean
     */
    protected function _deleteNoBroadcasting($key)
    {
        //próba usunięcia pliku
        try {
            unlink($this->_cache->getConfig()->path . '/' . $key);
        } catch (\Exception $e) {
            //nic
        }
        return true;
    }

    /**
     * Kasuje bufor bez rozgłaszania
     */
    protected function _deleteAllNoBroadcasting()
    {
        //iteracja po plikach
        foreach (glob($this->_cache->getConfig()->path . '/*') as $filename) {
            //usuwanie pliku
            try {
                is_file($filename) && unlink($filename);
            } catch (\Exception $e) {
                //nic
            }
        }
    }

}

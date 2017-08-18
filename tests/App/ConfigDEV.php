<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace App;

/**
 * Konfiguracja aplikacji DEV
 */
class ConfigDEV extends \Mmi\App\KernelConfig
{

    public function __construct()
    {
        //konfiguracja aplikacji
        $this->host = 'localhost';
        $this->salt = 'empty';
        $this->timeZone = 'Europe/Warsaw';

        //debugger 
        $this->debug = false;
        $this->compile = true;
        $this->languages = [];
        $this->plugins = [];

        $this->localCache = new \Mmi\Cache\CacheConfig;
        $this->localCache->handler = 'file';
        $this->localCache->path = BASE_PATH . '/var/cache';

        //ustawienia routera
        $this->router = (new \Mmi\Mvc\RouterConfig)->setRoute('test', '', ['module' => 'mmi', 'controller' => 'index', 'action' => 'test']);
        
        //konfiguracja bazy danych
        $this->db = new \Mmi\Db\DbConfig;
        $this->db->driver = 'sqlite';
    }

}

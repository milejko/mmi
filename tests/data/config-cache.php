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
class ConfigCACHE extends ConfigDEFAULT
{

    public function __construct()
    {
        parent::__construct();
        $this->debug = true;
       
        $this->localCache = new \Mmi\Cache\CacheConfig;
        $this->localCache->path = BASE_PATH . '/var/cache';

        $this->cache = new \Mmi\Cache\CacheConfig;
        $this->cache->path = BASE_PATH . '/var/cache';
    }

}

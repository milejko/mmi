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
class ConfigDEFAULT extends \Mmi\App\KernelConfig
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
        $this->languages = ['pl', 'en', 'de'];
        $this->plugins = ['App\TestFrontControllerPlugin'];

        //konfiguracja bazy danych
        $this->db = new \Mmi\Db\DbConfig;
        $this->db->driver = 'sqlite';
        $this->db->host = BASE_PATH . '/var/test-db.sqlite';
        
        $this->log = new \Mmi\Log\LogConfig;
        //logowanie debug
        $this->log->addInstance((new \Mmi\Log\LogConfigInstance)->setLevelDebug());
    }

}

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
 * Konfiguracja aplikacji z sesją
 */
class ConfigSESSION extends ConfigDEFAULT
{

    public function __construct()
    {
        parent::__construct();
        $this->session = new \Mmi\Session\SessionConfig;
        $this->session->name = 'test';
        $this->session->handler = 'user';
        $this->session->path = '\Mmi\Session\FileHandler';
    }

}

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
 * Konfiguracja aplikacji bez bazy
 */
class ConfigNODB extends ConfigDEFAULT
{

    public function __construct()
    {
        parent::__construct();
        //konfiguracja bazy danych
        $this->db = null;
    }

}

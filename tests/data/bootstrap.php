<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */
//definicja katalogu bazowego
define('BASE_PATH', __DIR__ . '/../../');

//dołączenie autoloadera
require BASE_PATH . 'vendor/autoload.php';

//powołanie konfiguracji i rejestru
require 'data/config-default.php';
require 'data/registry.php';
//mock
require 'data/auth-model.php';
require 'data/test-query-record.php';
require 'data/fc-plugin.php';

//iteracja po katalogach do utworzenia
foreach (['var/cache', 'var/compile', 'var/coverage', 'var/data', 'var/log', 'var/session'] as $dir) {
    //tworzenie katalogu
    !file_exists(BASE_PATH . '/' . $dir) ? mkdir(BASE_PATH . '/' . $dir, 0777, true) : null;
}

//kopiowanie testowej bazy danych do tmp
copy(BASE_PATH . '/tests/data/db.sqlite', BASE_PATH . '/var/test-db.sqlite');
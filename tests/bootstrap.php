<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */
//definicja katalogu bazowego
define('BASE_PATH', __DIR__ . '/../');

//dołączenie autoloadera
require BASE_PATH . 'vendor/autoload.php';

require 'App/ConfigDEV.php';
require 'App/Registry.php';

//iteracja po katalogach do utworzenia
foreach (['var/cache', 'var/compile', 'var/coverage', 'var/data', 'var/log', 'var/session'] as $dir) {
    //tworzenie katalogu
    !file_exists(BASE_PATH . '/' . $dir) ? mkdir(BASE_PATH . '/' . $dir, 0777, true) : null;
}
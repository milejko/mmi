<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

use Mmi\App\AppTesting;

//definicja katalogu bazowego
define('BASE_PATH', __DIR__ . '/../');

//dołączenie autoloadera
require BASE_PATH . 'vendor/autoload.php';

//zmienne testowe
putenv('APP_DEBUG_ENABLED=0');
putenv('DB_HOST=' . BASE_PATH . '/var/test-db.sqlite');
putenv('DB_DRIVER=sqlite');

//testowe obiekty
require BASE_PATH . 'tests/Mock/test-query.php';
require BASE_PATH . 'tests/Mock/test-record.php';
require BASE_PATH . 'tests/Mock/test-auth-provider.php';
if (!function_exists('apcu_fetch')) {
    require BASE_PATH . 'tests/Mock/apc-stub.php';
}
if (!class_exists(Redis::class)) {
    require BASE_PATH . 'tests/Mock/redis-stub.php';
}

//iteracja po katalogach do utworzenia
foreach (['var/cache', 'var/coverage', 'var/data', 'var/log', 'var/session'] as $dir) {
    //tworzenie katalogu
    !file_exists(BASE_PATH . '/' . $dir) ? mkdir(BASE_PATH . '/' . $dir, 0777, true) : null;
}

//kopiowanie testowej bazy danych do tmp
copy(BASE_PATH . '/tests/Mock/db.sqlite', BASE_PATH . '/var/test-db.sqlite');

//run application
(new AppTesting())->run();

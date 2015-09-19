<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */
//określanie ścieżki
define('BASE_PATH', __DIR__ . '/../');

//ładowanie autoloadera aplikacji
require BASE_PATH . '/vendor/autoload.php';

//powołanie i uruchomienie aplikacji
$app = new \Mmi\App\Kernel('\Mmi\App\Bootstrap');
$app->run();
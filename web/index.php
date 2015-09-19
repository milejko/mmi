<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */
//definicja katalogu bazowego
define('BASE_PATH', __DIR__ . '/../');

//dołączenie autoloadera
require BASE_PATH . 'vendor/autoload.php';

//uruchomienie aplikacji
(new \Mmi\App\Kernel('\Mmi\App\Bootstrap'))->run();

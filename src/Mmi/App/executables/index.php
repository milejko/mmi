<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 *
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2026 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

use Mmi\App\App;

//define base path
define('BASE_PATH', realpath(__DIR__ . '/../'));

//autoloader definition
require BASE_PATH . '/vendor/autoload.php';

//run application
(new App())->run();

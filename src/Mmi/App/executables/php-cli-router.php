<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 *
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2026 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

$requestedFile = __DIR__ . $_SERVER['PHP_SELF'];

// serve requested file as is, if exists
if (file_exists($requestedFile) && is_file($requestedFile)) {
    return false;
}

//MMi application file
require 'index.php';

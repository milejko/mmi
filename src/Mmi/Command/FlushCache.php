#!/usr/bin/env php
<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Command;

//nie ma tu jeszcze autoloadera ładowanie CliAbstract
require_once 'CommandAbstract.php';

/**
 * Całkowicie usuwa cache
 */
class FlushCache extends CommandAbstract {
	
	public function run() {
		//usuwanie cache
		\App\Registry::$cache->flush();
	}
	
}

//nowy obiekt usuwający cache
new FlushCache(isset($argv[1]) ? $argv[1] : null);
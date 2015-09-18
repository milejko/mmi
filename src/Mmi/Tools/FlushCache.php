<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Tools;

//nie ma tu jeszcze autoloadera ładowanie CliAbstract
require_once 'CliAbstract.php';

/**
 * Całkowicie usuwa cache
 */
class FlushCache extends CliAbstract {
	
	public function run() {
		//usuwanie cache
		\App\Registry::$cache->flush();
	}
	
}

//nowy obiekt usuwający cache
new FlushCache();
<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Command;

//nie ma tu jeszcze autoloadera ładowanie CliAbstract
require_once 'CommandAbstract.php';

/**
 * Wdrożenie bazy danych
 */
class DbDeploy extends CommandAbstract {
	
	/**
	 * Uruchomienie deployera
	 */
	public function run() {
		(new \Mmi\Db\Deployer)->deploy();
	}
	
}

new DbDeploy(isset($argv[1]) ? $argv[1] : null);
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
 * Renderer DAO, rekordów, zapytań itd.
 */
class DaoRenderer extends CommandAbstract {

	/**
	 * Metoda uruchamiająca
	 */
	public function run() {

		//odbudowanie wszystkich DAO/Record/Query/Field/Join
		foreach (\App\Registry::$db->tableList(\App\Registry::$config->db->schema) as $tableName) {
			//bez generowania dla DB_CHANGELOG
			if (strtoupper($tableName) == 'DB_CHANGELOG') {
				continue;
			}
			//buduje struktruę dla tabeli
			\Mmi\Orm\Builder::buildFromTableName($tableName);
		}
	}

}

//powołanie obiektu
new DaoRenderer('DEV');

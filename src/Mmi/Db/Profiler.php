<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Db;
use Monolog\Logger;

class Profiler extends \Mmi\App\Profiler {

	/**
	 * Dane profilera
	 * @var array
	 */
	protected static $_data = [];

	/**
	 * Licznik
	 * @var int
	 */
	protected static $_counter = 0;

	/**
	 * Licznik czasu
	 * @var int
	 */
	protected static $_elapsed = 0;

	/**
	 * Event query
	 * @param PDOStatement $statement
	 * @param array $bind
	 * @param float $elapsed
	 */
	public static function eventQuery(\PDOStatement $statement, array $bind, $elapsed = null) {
		//profiler wyłączony
		if (LoggerHelper::getLevel() > Logger::DEBUG) {
			return;
		}
		//zapytanie SQL bez bindów
		$sql = $statement->queryString;

		//ustalanie kluczy i wartości
		$keys = array_keys($bind);
		$values = array_values($bind);
		array_walk($values, function (&$v) {
			$v = '\'' . $v . '\'';
		});
		//iteracja po kluczach
		foreach ($keys as $key => $value) {
			//zamiana kluczy 
			if (is_int($value)) {
				$sql = preg_replace('/\?/', $values[$key], $sql, 1);
				continue;
			}
			$sql = str_replace(':' . trim($value, ':'), $values[$key], $sql);
		}
		return parent::event($sql, $elapsed);
	}

}

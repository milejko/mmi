<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\App;

use \Mmi\Log\LoggerHelper;
use \Monolog\Logger;

/**
 * Klasa profilera aplikacji
 */
class Profiler {

	/**
	 * Dane profilera
	 * @var array
	 */
	protected $_data = [];

	/**
	 * Licznik
	 * @var int
	 */
	protected $_counter = 0;

	/**
	 * Licznik czasu
	 * @var int
	 */
	protected $_elapsed = 0;

	/**
	 * Odcisk wywołania
	 * @var string
	 */
	protected $_runtimeStamp;

	/**
	 * Dodaje zdarzenie
	 * @param string $name nazwa
	 * @param string $elapsed opcjonalnie czas operacji
	 */
	public function event($name, $elapsed = null) {
		//profiler wyłączony
		if (LoggerHelper::getLevel() != Logger::DEBUG) {
			return;
		}
		//znacznik czasu
		$time = microtime(true);
		//obliczanie timestampu uruchomienia
		$this->_runtimeStamp = !$this->_runtimeStamp ? substr(md5($time . rand(0, 10000)), 6, 6) : $this->_runtimeStamp;
		if ($elapsed === null && $this->_counter > 0) {
			$elapsed = $time - $this->_data[$this->_counter - 1]['time'];
		} elseif ($elapsed === null) {
			$elapsed = 0;
		}
		$this->_data[$this->_counter] = [
			'name' => $name,
			'time' => $time,
			'elapsed' => $elapsed,
		];
		LoggerHelper::getLogger()->addDebug('{' . $this->_runtimeStamp . '} (' . number_format($elapsed, 6) . 's) ' . $name);
		$this->_elapsed += $elapsed;
		$this->_counter++;
	}

	/**
	 * Event query
	 * @param PDOStatement $statement
	 * @param array $bind
	 * @param float $elapsed
	 */
	public function eventQuery(\PDOStatement $statement, array $bind, $elapsed = null) {
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
		return $this->event('SQL: ' . $sql, $elapsed);
	}

	/**
	 * Pobiera dane z profilera
	 * @return array
	 */
	public function get() {
		//iteracja po danych
		foreach ($this->_data as $key => $item) {
			if ($this->_elapsed == 0) {
				$this->_data[$key]['percent'] = 0;
				continue;
			}
			$this->_data[$key]['percent'] = 100 * $item['elapsed'] / $this->_elapsed;
		}
		return $this->_data;
	}

	/**
	 * Zwraca ilość zdarzeń w profilerze
	 * @return int
	 */
	public function count() {
		return $this->_counter;
	}

	/**
	 * Pobiera sumaryczny czas wszystkich zdarzeń
	 * @return int
	 */
	public function elapsed() {
		return $this->_elapsed;
	}

}

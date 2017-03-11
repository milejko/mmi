<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\App;

use Mmi\Log\LoggerHelper,
	Monolog\Logger;

/**
 * Klasa profilera aplikacji
 */
class KernelProfiler {

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
	public function event($name) {
		//profiler wyłączony
		if (LoggerHelper::getLevel() != Logger::DEBUG) {
			return;
		}
		//znacznik czasu
		$time = microtime(true);
		//obliczanie timestampu uruchomienia
		$this->_runtimeStamp = !$this->_runtimeStamp ? substr(md5($time . rand(0, 10000)), 6, 6) : $this->_runtimeStamp;
		//obliczanie czasu trwania
		$elapsed = isset($this->_data[$this->_counter - 1]) ? ($time - $this->_data[$this->_counter - 1]['time']) : 0;
		//zapis rekordu
		$this->_data[$this->_counter] = [
			'name' => $name,
			'time' => $time,
			'elapsed' => $elapsed,
		];
		FrontController::getInstance()->getLogger()->addDebug('{' . $this->_runtimeStamp . '} (' . number_format($elapsed, 6) . 's) ' . $name);
		$this->_elapsed += $elapsed;
		$this->_counter++;
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

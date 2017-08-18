<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\App;

/**
 * Klasa profilera aplikacji
 */
class KernelProfiler implements KernelProfilerInterface
{

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
     * Dodaje zdarzenie
     * @param string $name nazwa
     * @param string $elapsed opcjonalnie czas operacji
     */
    public function event($name)
    {
        //znacznik czasu
        $time = microtime(true);
        //obliczanie czasu trwania
        $elapsed = isset($this->_data[$this->_counter - 1]) ? ($time - $this->_data[$this->_counter - 1]['time']) : 0;
        //zapis rekordu
        $this->_data[$this->_counter] = [
            'name' => $name,
            'time' => $time,
            'elapsed' => $elapsed,
        ];
        FrontController::getInstance()->getLogger()->debug($name . ' ' . number_format($elapsed, 6) . 's)');
        $this->_elapsed += $elapsed;
        $this->_counter++;
    }

    /**
     * Pobiera dane z profilera
     * @return array
     */
    public function get()
    {
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
    public function count()
    {
        return $this->_counter;
    }

    /**
     * Pobiera sumaryczny czas wszystkich zdarzeń
     * @return int
     */
    public function elapsed()
    {
        return $this->_elapsed;
    }

}

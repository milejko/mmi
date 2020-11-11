<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2020 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\App;

/**
 * Klasa profilera aplikacji
 */
class AppProfiler implements AppProfilerInterface
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
     */
    public function event(string $name): void
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
        $this->_elapsed += $elapsed;
        $this->_counter++;
    }

    /**
     * Pobiera dane z profilera
     */
    public function get(): array
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
     */
    public function count(): int
    {
        return $this->_counter;
    }

    /**
     * Pobiera sumaryczny czas wszystkich zdarzeń
     */
    public function elapsed(): float
    {
        return $this->_elapsed;
    }

}

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
 * Klasa pustego profilera aplikacji
 */
class NullKernelProfiler implements KernelProfilerInterface
{

    /**
     * Dodaje zdarzenie
     * @param string $name nazwa
     * @param string $elapsed opcjonalnie czas operacji
     */
    public function event($name)
    {
        //nic
    }

    /**
     * Pobiera dane z profilera
     * @return array
     */
    public function get()
    {
        return [];
    }

    /**
     * Zwraca ilość zdarzeń w profilerze
     * @return int
     */
    public function count()
    {
        return 0;
    }

    /**
     * Pobiera sumaryczny czas wszystkich zdarzeń
     * @return int
     */
    public function elapsed()
    {
        return 0;
    }

}

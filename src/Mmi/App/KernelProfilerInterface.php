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
 *
 * @deprecated since 3.9.0 to be removed in 4.0.0
 */
interface KernelProfilerInterface
{

    /**
     * Dodaje zdarzenie
     * @param string $name nazwa
     * @param string $elapsed opcjonalnie czas operacji
     */
    public function event($name);

    /**
     * Pobiera dane z profilera
     * @return array
     */
    public function get();

    /**
     * Zwraca ilość zdarzeń w profilerze
     * @return int
     */
    public function count();

    /**
     * Pobiera sumaryczny czas wszystkich zdarzeń
     * @return int
     */
    public function elapsed();
}

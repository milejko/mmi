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
interface AppProfilerInterface
{

    /**
     * Dodaje zdarzenie
     */
    public function event(string $name): void;

    /**
     * Pobiera dane z profilera
     */
    public function get(): array;

    /**
     * Zwraca ilość zdarzeń w profilerze
     */
    public function count(): int;

    /**
     * Pobiera sumaryczny czas wszystkich zdarzeń
     */
    public function elapsed(): float;
}

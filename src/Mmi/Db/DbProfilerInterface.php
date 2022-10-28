<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Db;

use PDOStatement;

/**
 * Db profiler interface
 */
interface DbProfilerInterface
{
    /**
     * Rejestruje zdarzeni
     * @param PDOStatement $statement
     * @param array $bind
     * @param float $elapsed
     */
    public function event(PDOStatement $statement, array $bind, $elapsed = null): void;

    /**
     * Pobiera dane z profilera
     */
    public function get(): array;

    /**
     * Zwraca ilość zapytań w profilerze
     */
    public function count(): int;

    /**
     * Pobiera sumaryczny czas wszystkich zapytań
     */
    public function elapsed(): float;
}

<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Db;

use Mmi\App\AppProfilerInterface;

/**
 * Klasa profilera aplikacji
 */
class DbProfiler implements DbProfilerInterface
{
    public const EVENT_PREFIX = 'Mmi\Db\Adapter\Pdo';

    /**
     * Dane profilera
     * @var array
     */
    protected $_data = [];

    /**
     * @var AppProfilerInterface
     */
    private $profiler;

    /**
     * Constructor
     */
    public function __construct(AppProfilerInterface $profiler)
    {
        $this->profiler = $profiler;
    }

    /**
     * Rejestruje zdarzeni
     * @param PDOStatement $statement
     * @param array $bind
     * @param float $elapsed
     */
    public function event(\PDOStatement $statement, array $bind, $elapsed = null): void
    {
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
        //zapis rekordu
        $this->_data[] = [
            'sql' => $sql,
            'elapsed' => $elapsed,
        ];
        //event profilera
        $this->profiler->event(self::EVENT_PREFIX . ': ' . substr($sql, 0, strpos($sql, ' ')));
    }

    /**
     * Pobiera dane z profilera
     */
    public function get(): array
    {
        $elapsed = $this->elapsed();
        //iteracja po danych
        foreach ($this->_data as $key => $item) {
            if ($elapsed == 0) {
                $this->_data[$key]['percent'] = 0;
                continue;
            }
            $this->_data[$key]['percent'] = 100 * $item['elapsed'] / $elapsed;
        }
        return $this->_data;
    }

    /**
     * Zwraca ilość zapytań w profilerze
     */
    public function count(): int
    {
        return count($this->_data);
    }

    /**
     * Pobiera sumaryczny czas wszystkich zapytań
     */
    public function elapsed(): float
    {
        $elapsed = 0;
        foreach ($this->_data as $record) {
            $elapsed += $record['elapsed'];
        }
        return $elapsed;
    }
}

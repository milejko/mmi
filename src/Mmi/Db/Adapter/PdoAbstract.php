<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Db\Adapter;

use Mmi\Db\DbConfig;
use Mmi\Db\DbException;
use Mmi\Db\DbInterface;
use Mmi\Db\DbProfiler;
use PDOStatement;

/**
 * Abstrakcyjna klasa adaptera PDO
 */
abstract class PdoAbstract implements DbInterface
{

    /**
     * Obiekt \PDO do pobierania danych
     * @var \PDO
     */
    protected $_downstreamPdo;

    /**
     * Obiekt \PDO do wysyłania daych
     * @var \PDO
     */
    protected $_upstreamPdo;

    /**
     * Konfiguracja
     * @var \Mmi\Db\DbConfig
     */
    protected $_config;

    /**
     * Profiler
     * @var \Mmi\Db\DbProfiler
     */
    protected $_profiler;

    /**
     * Stan połączenia
     * @var boolean
     */
    protected $_connected = false;

    /**
     * Stan transakcji
     * @var boolean
     */
    protected $_transactionInProgress = false;

    /**
     * Konstruktor wczytujący konfigurację
     */
    public function __construct(DbConfig $config)
    {
        $this->_config = $config;
    }

    /**
     * Tworzy połączenie z bazą danych
     */
    abstract public function connect(): self;

    /**
     * Zwraca informację o kolumnach tabeli
     * @param string $tableName nazwa tabeli
     * @param array $schema schemat
     * @return array
     */
    abstract public function tableInfo(string $tableName, string $schema = null): array;

    /**
     * Listuje tabele w schemacie bazy danych
     * @param string $schema
     * @return array
     */
    abstract public function tableList(string $schema = null): array;

    /**
     * Ustawia domyślne parametry dla importu (długie zapytania)
     */
    abstract public function setDefaultImportParams(): self;

    /**
     * Zwraca konfigurację
     */
    public final function getConfig(): DbConfig
    {
        return $this->_config;
    }

    /**
     * Nieistniejące metody
     * @param string $method
     * @param array $params
     * @throws DbException
     */
    public final function __call($method, array $params = [])
    {
        throw new DbException(get_called_class() . ': method not found: ' . $method);
    }

    /**
     * Zwraca opakowaną cudzysłowami wartość
     * @see \PDO::quote()
     * @see \PDO::PARAM_STR
     * @see \PDO::PARAM_INT
     */
    protected final function quote(string $value, int $paramType = \PDO::PARAM_STR): string
    {
        //łączy jeśli niepołączony
        if (!$this->_connected) {
            $this->connect();
        }
        //dla szczególnych typów: null int i bool nie opakowuje
        switch (gettype($value)) {
            case 'NULL':
                return 'NULL';
            case 'integer':
                return intval($value);
            case 'boolean':
                return $value ? 'true' : 'false';
        }
        //quote z PDO
        return $this->_downstreamPdo->quote($value, $paramType);
    }

    /**
     * Wydaje zapytanie \PDO prepare, execute
     * rzuca wyjątki
     * @throws DbException
     */
    public final function query(string $sql, array $bind = []): PDOStatement
    {
        //łączy jeśli niepołączony
        if (!$this->_connected) {
            $this->connect();
        }
        //czas startu na potrzeby profilera
        $start = microtime(true);
        //wybór PDO (zapis lub odczyt)
        $pdo = (preg_match('/^SELECT/i', $sql) && !$this->_transactionInProgress) ? $this->_downstreamPdo : $this->_upstreamPdo;
        //przygotowywanie zapytania
        $statement = $pdo->prepare($sql);
        //błędne zapytanie
        if (!$statement) {
            $error = $pdo->errorInfo();
            throw new DbException(get_called_class() . ': ' . (isset($error[2]) ? $error[2] : $error[0]) . ' --- ' . $sql);
        }
        //wiązanie parametrów do zapytania
        foreach ($bind as $key => $param) {
            //domyślnie typ jako string
            $type = \PDO::PARAM_STR;
            //jeśli bool to bool
            if (is_bool($param)) {
                $type = \PDO::PARAM_BOOL;
            }
            //jeśli kluczem jest liczba (przy insertach), zwiększamy licznik o 1
            if (is_int($key)) {
                $key = $key + 1;
            }
            //wiązanie wartości
            $statement->bindValue($key, $param, $type);
        }
        //wykonywanie zapytania
        $result = $statement->execute();
        //przypisanie rezultatu do zmiennej w statement
        $statement->result = $result;
        //jeśli rezultat to nie 1, wyrzucony zostaje wyjątek bazodanowy
        if ($result != 1) {
            $error = $statement->errorInfo();
            $error = isset($error[2]) ? $error[2] : $error[0];
            throw new DbException(get_called_class() . ': ' . $error . ' --- ' . $sql);
        }
        //jeśli profiler włączony, dodanie eventu
        $this->_profiler ? $this->_profiler->event($statement, $bind, microtime(true) - $start) : null;
        return $statement;
    }

    /**
     * Zwraca ostatnio wstawione ID
     * @return mixed
     */
    public final function lastInsertId()
    {
        //łączy jeśli niepołączony
        if (!$this->_connected) {
            $this->connect();
        }
        return $this->_upstreamPdo->lastInsertId();
    }

    /**
     * Zwraca wszystkie rekordy (rządki)
     */
    public final function fetchAll(string $sql, array $bind = []): array
    {
        return $this->query($sql, $bind)->fetchAll(\PDO::FETCH_NAMED);
    }

    /**
     * Zwraca pierwszy rekord (rządek)
     */
    public final function fetchRow(string $sql, array $bind = []): array
    {
        return $this->query($sql, $bind)->fetch(\PDO::FETCH_NAMED);
    }

    /**
     * Wstawianie rekordu
     */
    public final function insert(string $table, array $data = []): int
    {
        $fields = '';
        $values = '';
        $bind = [];
        //wiązanie placeholderów "?" w zapytaniu z parametrami do wstawienia
        foreach ($data as $key => $value) {
            $fields .= $this->prepareField($key) . ', ';
            $values .= '?, ';
            $bind[] = $value;
        }
        //budowanie wstawiającego SQL
        $sql = 'INSERT INTO ' . $this->prepareTable($table) . ' (' . rtrim($fields, ', ') . ') VALUES(' . rtrim($values, ', ') . ')';
        return $this->query($sql, $bind)->rowCount();
    }

    abstract public function insertAll(string $table, array $data = []): int;

    /**
     * Aktualizacja rekordów
     */
    public final function update(string $table, array $data = [], string $where = '', array $whereBind = []): int
    {
        $fields = '';
        $bind = [];
        //jeśli brak danych wyjście z metody
        if (empty($data)) {
            return 1;
        }
        //wiązanie parametrów
        foreach ($data as $key => $value) {
            $bindKey = PdoBindHelper::generateBindKey();
            $fields .= $this->prepareField($key) . ' = :' . $bindKey . ', ';
            $bind[$bindKey] = $value;
        }
        //budowanie zapytania aktualizującego
        $sql = 'UPDATE ' . $this->prepareTable($table) . ' SET ' . rtrim($fields, ', ') . ' ' . $where;
        return $this->query($sql, array_merge($bind, $whereBind))->rowCount();
    }

    /**
     * Kasowanie rekordu
     */
    public final function delete(string $table, string $where = '', array $whereBind = []): int
    {
        return $this->query('DELETE FROM ' . $this->prepareTable($table) . ' ' . $where, $whereBind)
                ->rowCount();
    }

    /**
     * Pobieranie rekordów
     */
    public final function select(
        string $fields = '*', 
        string $from = '',
        string $where = null,
        string $groupBy = null,
        string $order = null,
        int $limit = null,
        int $offset = null,
        array $whereBind = []
    ): array
    {
        $sql = 'SELECT' .
            ' ' . $fields .
            ' FROM' .
            ' ' . $from .
            ' ' . $where .
            ' ' . $groupBy .
            ' ' . $order .
            ' ' . $this->prepareLimit($limit, $offset);
        return $this->fetchAll($sql, $whereBind);
    }

    /**
     * Rozpoczyna transakcję
     * @return boolean
     */
    public final function beginTransaction(): bool
    {
        //łączenie jeśli niepołączony
        if (!$this->_connected) {
            $this->connect();
        }
        //rozpoczęcie transakcji
        $this->_transactionInProgress = true;
        return $this->_upstreamPdo->beginTransaction();
    }

    /**
     * Zatwierdza transakcję
     * @return boolean
     */
    public final function commit(): bool
    {
        //brak transakcji
        if (!$this->_transactionInProgress) {
            return false;
        }
        //zakończenie transakcji z zatwierdzeniem
        $this->_transactionInProgress = false;
        return $this->_upstreamPdo->commit();
    }

    /**
     * Odrzuca transakcję
     */
    public final function rollBack(): bool
    {
        //brak transakcji
        if (!$this->_transactionInProgress) {
            return false;
        }
        //zakończenie transakcji z cofnięciem
        $this->_transactionInProgress = false;
        return $this->_upstreamPdo->rollBack();
    }

    /**
     * Tworzy warunek limit
     * @param int $limit
     * @param int $offset
     * @return string
     */
    public final function prepareLimit($limit = null, $offset = null): ?string
    {
        //wyjście jeśli brak limitu
        if (!($limit > 0)) {
            return null;
        }
        //limit z offsetem
        if ($offset > 0) {
            return 'LIMIT ' . intval($offset) . ', ' . intval($limit);
        }
        //sam limit
        return 'LIMIT ' . intval($limit);
    }


    /**
     * Tworzy konstrukcję sprawdzającą ILIKE, jeśli dostępna w silniku
     */
    public final function prepareLike(string $fieldName): string
    {
        //filed like
        return $fieldName . ' LIKE';
    }

    /**
     * Null check
     */
    abstract public function prepareNullCheck(string $fieldName, bool $positive = true): string;

    /**
     * Ustawia profiler
     */
    public final function setProfiler(DbProfiler $profiler): self
    {
        $this->_profiler = $profiler;
        return $this;
    }

    /**
     * Zwraca profiler
     */
    public final function getProfiler(): DbProfiler
    {
        return $this->_profiler;
    }

}

<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2020 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Db;

use PDOStatement;

/**
 * Database service interface
 */
interface DbInterface
{
    /**
     * Konstruktor wczytujący konfigurację
     */
    public function __construct(DbConfig $config);

    /**
     * Tworzy połączenie z bazą danych
     */
    public function connect(): self;

    /**
     * Zwraca informację o kolumnach tabeli
     */
    public function tableInfo(string $tableName, string $schema = null): array;

    /**
     * Listuje tabele w schemacie bazy danych
     */
    public function tableList(string $schema = null): array;

    /**
     * Ustawia domyślne parametry dla importu (długie zapytania)
     */
    public function setDefaultImportParams(): self;

    /**
     * Null check
     */
    public function prepareNullCheck(string $fieldName, bool $positive = true): string;

    /**
     * Prepare field
     */
    public function prepareTable(string $tableName): string;

    /**
     * Prepare field
     */
    public function prepareField(string $fieldName): string;

    /**
     * Like
     */
    public function prepareLike(string $fieldName): string;

    /**
     * Prepare limit
     */
    public function prepareLimit($limit = null, $offset = null): ?string;

    /**
     * Zwraca konfigurację
     */
    public function getConfig(): DbConfig;

    /**
     * Wydaje zapytanie \PDO prepare, execute
     * rzuca wyjątki
     * @see \PDO::prepare()
     * @see \PDO::execute()
     */
    public function query(string $sql, array $bind = []): PDOStatement;

    /**
     * Zwraca ostatnio wstawione ID
     * @return mixed
     */
    public function lastInsertId();

    /**
     * Zwraca wszystkie rekordy (rządki)
     * @param array $bind tabela w formacie akceptowanym przez \PDO::prepare()
     */
    public function fetchAll(string $sql, array $bind = []): array;

    /**
     * Zwraca pierwszy rekord (rządek)
     * @param string $sql zapytanie
     * @param array $bind tabela w formacie akceptowanym przez \PDO::prepare()
     * @return array
     */
    public function fetchRow(string $sql, array $bind = []): array;

    /**
     * Wstawianie rekordu
     * @param array $data tabela w postaci: klucz => wartość
     */
    public function insert(string $table, array $data = []): int;

    /**
     * Wstawianie wielu rekordów
     * @param string $table nazwa tabeli
     * @param array $data tabela tabel w postaci: klucz => wartość
     * @return integer
     */
    public function insertAll(string $table, array $data = []): int;

    /**
     * Aktualizacja rekordów
     * @param string $table nazwa tabeli
     * @param array $whereBind warunek w postaci zagnieżdżonego bind
     */
    public function update(string $table, array $data = [], string $where = '', array $whereBind = []): int;

    /**
     * Kasowanie rekordu
     * @param array $whereBind warunek w postaci zagnieżdżonego bind
     */
    public function delete(string $table, string $where = '', array $whereBind = []): int;

    /**
     * Pobieranie rekordów
     * @param string $fields pola do wybrania
     * @param array $whereBind parametry
     */
    public function select(
        string $fields = '*',
        string $from = '',
        string $where = null,
        string $groupBy = null,
        string $order = null,
        int $limit = null,
        int $offset = null,
        array $whereBind = []
    ): array;

    /**
     * Rozpoczyna transakcję
     */
    public function beginTransaction(): bool;

    /**
     * Zatwierdza transakcję
     */
    public function commit(): bool;

    /**
     * Odrzuca transakcję
     */
    public function rollBack(): bool;

    /**
     * Ustawia profiler
     */
    public function setProfiler(DbProfiler $profiler): self;

    /**
     * Zwraca profiler
     */
    public function getProfiler(): DbProfiler;
}

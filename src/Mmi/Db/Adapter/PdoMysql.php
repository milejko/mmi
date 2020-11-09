<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Db\Adapter;

class PdoMysql extends PdoAbstract
{

    /**
     * Ustawia domyślne parametry dla importu (długie zapytania)
     */
    public function setDefaultImportParams(): self
    {
        $this->query('
            SET NAMES utf8;
            SET time_zone = \'+00:00\';
            SET foreign_key_checks = 0;
            SET sql_mode = \'NO_AUTO_VALUE_ON_ZERO\';
        ');
        return $this;
    }

    /**
     * Tworzy połączenie z bazą danych
     */
    public function connect(): self
    {
        $this->_config->port = $this->_config->port ? $this->_config->port : 3306;
        //nowy obiekt PDO do odczytu danych
        $this->_downstreamPdo = new \PDO(
            $this->_config->driver . ':host=' . $this->_config->host . ';port=' . $this->_config->port . ';dbname=' . $this->_config->name . ';charset=utf8', $this->_config->user, $this->_config->password, [\PDO::ATTR_PERSISTENT => $this->_config->persistent]
        );
        //nowy obiekt pdo do zapisu danych
        $this->_upstreamPdo = new \PDO(
            $this->_config->driver . ':host=' . ($this->_config->upstreamHost ? $this->_config->upstreamHost : $this->_config->host) . ';port=' . ($this->_config->upstreamPort ? $this->_config->upstreamPort : $this->_config->port) . ';dbname=' . $this->_config->name . ';charset=utf8', $this->_config->user, $this->_config->password, [\PDO::ATTR_PERSISTENT => $this->_config->persistent]
        );
        //zmiana stanu na połączony
        $this->_connected = true;
        return $this;
    }

    /**
     * Otacza nazwę pola odpowiednimi znacznikami
     * @param string $fieldName nazwa pola
     * @return string
     */
    public function prepareField(string $fieldName): string
    {
        //funkcja sortująca
        if ($fieldName == 'RAND()') {
            return 'RAND()';
        }
        //dla mysql `
        if (false === strpos($fieldName, '`')) {
            return '`' . str_replace('.', '`.`', $fieldName) . '`';
        }
        return $fieldName;
    }

    /**
     * Otacza nazwę tabeli odpowiednimi znacznikami
     * @param string $tableName nazwa tabeli
     * @return string
     */
    public function prepareTable(string $tableName): string
    {
        //dla mysql tak jak pola
        return $this->prepareField($tableName);
    }

    /**
     * Zwraca informację o kolumnach tabeli
     */
    public function tableInfo(string $tableName, string $schema = null): array
    {
        return $this->_associateTableMeta($this->fetchAll('SELECT `column_name` as `name`, `data_type` AS `dataType`, `character_maximum_length` AS `maxLength`, `is_nullable` AS `null`, `column_default` AS `default`, `extra` AS `extra`, `column_key` AS `column_key` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `table_name` = :name AND `table_schema` = :schema ORDER BY `ordinal_position`', [
                    ':name' => $tableName,
                    ':schema' => $this->_config->name
        ]));
    }

    /**
     * Listuje tabele w schemacie bazy danych
     */
    public function tableList(string $schema = null): array
    {
        $list = $this->fetchAll('SHOW TABLES;');
        $tables = [];
        foreach ($list as $row) {
            foreach ($row as $name) {
                $tables[] = $name;
            }
        }
        return $tables;
    }

    /**
     * Wstawianie wielu rekordów
     */
    public function insertAll(string $table, array $data = []): int
    {
        $fields = '';
        $fieldsCompleted = false;
        $values = '';
        $bind = [];
        //dla każdego rekordu te same operacje
        foreach ($data as $row) {
            if (empty($row)) {
                continue;
            }
            $cur = '';
            //wiązanie placeholderów "?" w zapytaniu z parametrami do wstawienia
            foreach ($row as $key => $value) {
                if (!$fieldsCompleted) {
                    $fields .= $this->prepareField($key) . ', ';
                }
                $cur .= '?, ';
                $bind[] = $value;
            }
            $values .= '(' . rtrim($cur, ', ') . '), ';
            $fieldsCompleted = true;
        }
        $sql = 'INSERT INTO ' . $this->prepareTable($table) . ' (' . rtrim($fields, ', ') . ') VALUES ' . rtrim($values, ', ');
        return $this->query($sql, $bind)->rowCount();
    }

    /**
     * Tworzy konstrukcję sprawdzającą null w silniku bazy danych
     * @param string $fieldName nazwa pola
     * @param boolean $positive sprawdza czy null, lub czy nie null
     * @return string 
     */
    protected function prepareNullCheck(string $fieldName, bool $positive = true): string
    {
        return ($positive ? '' : '!') . 'ISNULL(' . $fieldName . ')';
    }

    /**
     * Tworzy konstrukcję sprawdzającą ILIKE, jeśli dostępna w silniku
     * @param string $fieldName nazwa pola
     * @return string
     */
    protected function prepareLike(string $fieldName): string
    {
        return $fieldName . ' LIKE';
    }

    /**
     * Konwertuje do tabeli asocjacyjnej meta dane tabel
     * @param array $meta meta data
     * @return array
     */
    private function _associateTableMeta(array $meta): array
    {
        $associativeMeta = [];
        foreach ($meta as $column) {
            //przekształcanie odpowiedzi do standardowej postaci
            $associativeMeta[$column['name']] = [
                'dataType' => $column['dataType'],
                'maxLength' => $column['maxLength'],
                'null' => ($column['null'] == 'YES') ? true : false,
                'default' => $column['default']
            ];
        }
        return $associativeMeta;
    }

}

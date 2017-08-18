<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Db\Adapter;

class PdoPgsql extends PdoAbstract
{

    /**
     * Ustawia schemat
     * @param string $schemaName nazwa schematu
     * @return \Mmi\Db\Adapter\PdoPgsql
     */
    public function selectSchema($schemaName)
    {
        //ustawienie schematu
        $this->_config->schema = $schemaName;
        //ustawienie zmiennej środowiskowej search_path
        $this->query('SET search_path TO "' . $schemaName . '"');
        //zwrot siebie
        return $this;
    }

    /**
     * Ustawia domyślne parametry dla importu (długie zapytania)
     * @return \Mmi\Db\Adapter\PdoPgsql
     */
    public function setDefaultImportParams()
    {
        //timeout 0
        $this->query('SET statement_timeout = 0;');
        $this->query('SET standard_conforming_strings = on;');
        //sprawdzanie treści funkcji wyłączone
        $this->query('SET check_function_bodies = false;');
        //poziom błędów: warning
        $this->query('SET client_min_messages = warning;');
        $this->query('SET default_with_oids = false;');
        //brak domyślnego schematu
        $this->query('SET default_tablespace = \'\';');
        return $this;
    }

    /**
     * Tworzy połączenie z bazą danych
     */
    public function connect()
    {
        //domyślny port
        $this->_config->port = $this->_config->port ? $this->_config->port : 5432;
        //nowy obiekt PDO do odczytu danych
        $this->_downstreamPdo = new \PDO(
            $this->_config->driver . ':host=' . $this->_config->host . ';port=' . $this->_config->port . ';dbname=' . $this->_config->name, $this->_config->user, $this->_config->password, [\PDO::ATTR_PERSISTENT => $this->_config->persistent]
        );
        //nowy obiekt pdo do zapisu danych
        $this->_upstreamPdo = new \PDO(
            $this->_config->driver . ':host=' . ($this->_config->upstreamHost ? $this->_config->upstreamHost : $this->_config->host) . ';port=' . ($this->_config->upstreamPort ? $this->_config->upstreamPort : $this->_config->port) . ';dbname=' . $this->_config->name, $this->_config->user, $this->_config->password, [\PDO::ATTR_PERSISTENT => $this->_config->persistent]
        );
        //zmiana stanu na połączony
        $this->_connected = true;
        //ustawianie schematu
        if ($this->_config->schema) {
            $this->selectSchema($this->_config->schema);
        } else {
            //domyślnie public
            $this->_config->schema = 'public';
        }
        return $this;
    }

    /**
     * Otacza nazwę pola odpowiednimi znacznikami
     * @param string $fieldName nazwa pola
     * @return string
     */
    public function prepareField($fieldName)
    {
        //dla postgresql "
        if (strpos($fieldName, '"') === false) {
            //"
            return '"' . str_replace('.', '"."', $fieldName) . '"';
        }
        return $fieldName;
    }

    /**
     * Otacza nazwę tabeli odpowiednimi znacznikami
     * @param string $tableName nazwa tabeli
     * @return string
     */
    public function prepareTable($tableName)
    {
        //dla postgresql "
        return $this->prepareField($tableName);
    }

    /**
     * Tworzy warunek limit
     * @param int $limit
     * @param int $offset
     * @return string
     */
    public function prepareLimit($limit = null, $offset = null)
    {
        //brak limitu
        if (!($limit > 0)) {
            return;
        }
        //istnieje offset
        if ($offset > 0) {
            return ' LIMIT ' . intval($limit) . ' OFFSET ' . intval($offset);
        }
        //sam limit
        return ' LIMIT ' . intval($limit);
    }

    /**
     * Tworzy konstrukcję sprawdzającą null w silniku bazy danych
     * @param string $fieldName nazwa pola
     * @param boolean $positive sprawdza czy null, lub czy nie null
     * @return string
     */
    public function prepareNullCheck($fieldName, $positive = true)
    {
        //zapytanie o null
        if ($positive) {
            return $fieldName . ' ISNULL';
        }
        //o nie null
        return $fieldName . ' NOTNULL';
    }

    /**
     * Zwraca informację o kolumnach tabeli
     * @param string $tableName nazwa tabeli
     * @param array $schema schemat
     * @return array
     */
    public function tableInfo($tableName, $schema = null)
    {
        //pobranie danych
        $tableInfo = $this->fetchAll('SELECT "column_name" as "name", "data_type" AS "dataType", "character_maximum_length" AS "maxLength", "is_nullable" AS "null", "column_default" AS "default" FROM INFORMATION_SCHEMA.COLUMNS WHERE "table_name" = :name AND "table_schema" = :schema ORDER BY "ordinal_position"', [
            ':name' => $tableName,
            ':schema' => ($schema) ? $schema : ($this->_config->schema ? $this->_config->schema : 'public')
        ]);
        //zwrot sformatowanych danych
        return $this->_associateTableMeta($tableInfo);
    }

    /**
     * Listuje tabele w schemacie bazy danych
     * @param string $schema
     * @return array
     */
    public function tableList($schema = null)
    {
        //pobranie listy tabel
        $list = $this->fetchAll('SELECT table_name as name
			FROM information_schema.tables
			WHERE table_schema = :schema
			ORDER BY table_name', [':schema' => ($schema) ? $schema : ($this->_config->schema ? $this->_config->schema : 'public')]);
        $tables = [];
        //iteracja tabelach
        foreach ($list as $row) {
            $tables[] = $row['name'];
        }
        return $tables;
    }

    /**
     * Tworzy konstrukcję sprawdzającą ILIKE, jeśli dostępna w silniku
     * @param string $fieldName nazwa pola
     * @return string
     */
    public function prepareIlike($fieldName)
    {
        //rzutowanie tekstu
        return 'CAST(' . $fieldName . ' AS text) ILIKE';
    }

}

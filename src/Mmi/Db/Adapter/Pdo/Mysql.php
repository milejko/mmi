<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Db\Adapter\Pdo;

class Mysql extends PdoAbstract {

	/**
	 * Ustawia schemat
	 * @param string $schemaName nazwa schematu
	 * @return \Mmi\Db\Adapter\Pdo\Mysql
	 */
	public function selectSchema($schemaName) {
		return $this;
	}

	/**
	 * Ustawia domyślne parametry dla importu (długie zapytania)
	 * @return \Mmi\Db\Adapter\Pdo\Mysql
	 */
	public function setDefaultImportParams() {
		$this->query('SET NAMES utf8;
			SET time_zone = \'SYSTEM\';
			SET sql_mode = \'NO_AUTO_VALUE_ON_ZERO\';
		');
		return $this;
	}

	/**
	 * Tworzy połączenie z bazą danych
	 */
	public function connect() {
		$this->_config->port = $this->_config->port ? $this->_config->port : 3306;
		parent::connect();
		return $this;
	}

	/**
	 * Otacza nazwę pola odpowiednimi znacznikami
	 * @param string $fieldName nazwa pola
	 * @return string
	 */
	public function prepareField($fieldName) {
		//dla mysql `
		if (strpos($fieldName, '`') === false) {
			return '`' . str_replace('.', '`.`', $fieldName) . '`';
		}
		return $fieldName;
	}

	/**
	 * Otacza nazwę tabeli odpowiednimi znacznikami
	 * @param string $tableName nazwa tabeli
	 * @return string
	 */
	public function prepareTable($tableName) {
		//dla mysql tak jak pola
		return $this->prepareField($tableName);
	}

	/**
	 * Zwraca informację o kolumnach tabeli
	 * @param string $tableName nazwa tabeli
	 * @param array $schema schemat nieistotny w MySQL
	 * @return array
	 */
	public function tableInfo($tableName, $schema = null) {
		return $this->_associateTableMeta($this->fetchAll('SELECT `column_name` as `name`, `data_type` AS `dataType`, `character_maximum_length` AS `maxLength`, `is_nullable` AS `null`, `column_default` AS `default`, `extra` AS `extra`, `column_key` AS `column_key` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `table_name` = :name AND `table_schema` = :schema ORDER BY `ordinal_position`', [
					':name' => $tableName,
					':schema' => $this->_config->name
		]));
	}

	/**
	 * Listuje tabele w schemacie bazy danych
	 * @param string $schema nie istotny w MySQL
	 * @return array
	 */
	public function tableList($schema = null) {
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
	 * Tworzy konstrukcję sprawdzającą null w silniku bazy danych
	 * @param string $fieldName nazwa pola
	 * @param boolean $positive sprawdza czy null, lub czy nie null
	 * @return string 
	 */
	public function prepareNullCheck($fieldName, $positive = true) {
		return ($positive ? '' : '!') . 'ISNULL(' . $fieldName . ')';
	}

	/**
	 * Tworzy konstrukcję sprawdzającą ILIKE, jeśli dostępna w silniku
	 * @param string $fieldName nazwa pola
	 * @return string
	 */
	public function prepareIlike($fieldName) {
		return $fieldName . ' LIKE';
	}

}

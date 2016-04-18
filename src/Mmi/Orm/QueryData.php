<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Orm;

/**
 * Klasa danych zapytania
 */
class QueryData {

	/**
	 * Obiekt zapytania
	 * @var \Mmi\Orm\Query
	 */
	protected $_query;

	/**
	 * Konstruktor
	 * @param \Mmi\Orm\Query $query
	 */
	public function __construct(\Mmi\Orm\Query $query) {
		$this->_query = $query;
	}

	/**
	 * Pobiera ilość rekordów
	 * @return int
	 */
	public final function count($column = '*') {
		//wykonanie zapytania zliczającego na adapter
		$result = \Mmi\Orm\DbConnector::getAdapter()->select('COUNT(' . ($column === '*' ? '*' : \Mmi\Orm\DbConnector::getAdapter()->prepareField($column)) . ')', $this->_prepareFrom(), $this->_query->getQueryCompile()->where, $this->_query->getQueryCompile()->groupBy, '', null, null, $this->_query->getQueryCompile()->bind);
		return isset($result[0]) ? current($result[0]) : 0;
	}

	/**
	 * Pobiera wszystkie rekordy i zwraca ich kolekcję
	 * @return \Mmi\Orm\RecordCollection
	 */
	public final function find() {
		//odpytanie adaptera o rekordy
		$result = \Mmi\Orm\DbConnector::getAdapter()->select($this->_prepareFields(), $this->_prepareFrom(), $this->_query->getQueryCompile()->where, $this->_query->getQueryCompile()->groupBy, $this->_query->getQueryCompile()->order, $this->_query->getQueryCompile()->limit, $this->_query->getQueryCompile()->offset, $this->_query->getQueryCompile()->bind);
		//ustalenie klasy rekordu
		$recordName = $this->_query->getRecordName();
		$records = [];
		//tworzenie rekordów
		foreach ($result as $row) {
			$record = new $recordName;
			/* @var $record \Mmi\Orm\Record */
			$record->setFromArray($row)->clearModified();
			$records[] = $record;
		}
		//ustalenie klasy kolekcji rekordów
		$collectionName = $this->_query->getCollectionName();
		//zwrot kolekcji
		return new $collectionName($records);
	}

	/**
	 * Pobiera obiekt pierwszy ze zbioru
	 * null jeśli brak danych
	 * @param \Mmi\Orm\Query $q Obiekt zapytania
	 * @return \Mmi\Orm\RecordRo
	 */
	public final function findFirst() {
		//odpytanie adaptera o rekordy
		$result = \Mmi\Orm\DbConnector::getAdapter()->select($this->_prepareFields(), $this->_prepareFrom(), $this->_query->getQueryCompile()->where, $this->_query->getQueryCompile()->groupBy, $this->_query->getQueryCompile()->order, 1, $this->_query->getQueryCompile()->offset, $this->_query->getQueryCompile()->bind);
		//null jeśli brak danych
		if (!is_array($result) || !isset($result[0])) {
			return null;
		}
		//ustalenie klasy rekordu
		$recordName = $this->_query->getRecordName();
		/* @var $record \Mmi\Orm\RecordRo */
		$record = new $recordName;
		return $record->setFromArray($result[0])->clearModified();
	}

	/**
	 * Zwraca tablicę asocjacyjną (pary)
	 * @param string $keyName
	 * @param string $valueName
	 * @return array
	 */
	public final function findPairs($keyName, $valueName) {
		//odpytanie adaptera o rekordy
		$data = \Mmi\Orm\DbConnector::getAdapter()->select(\Mmi\Orm\DbConnector::getAdapter()->prepareField($keyName) . ', ' . \Mmi\Orm\DbConnector::getAdapter()->prepareField($valueName), $this->_prepareFrom(), $this->_query->getQueryCompile()->where, $this->_query->getQueryCompile()->groupBy, $this->_query->getQueryCompile()->order, $this->_query->getQueryCompile()->limit, $this->_query->getQueryCompile()->offset, $this->_query->getQueryCompile()->bind);
		$kv = [];
		foreach ($data as $line) {
			//przy wybieraniu tych samych pól tabela ma tylko jeden wiersz
			if (count($line) == 1) {
				$line = current($line);
			}
			//klucz to pierwszy element, wartość - drugi
			$kv[current($line)] = next($line);
		}
		return $kv;
	}

	/**
	 * Pobiera wartość maksymalną z kolumny
	 * @param string $keyName nazwa klucza
	 * @return string wartość maksymalna
	 */
	public final function findMax($keyName) {
		//odpytanie adaptera o rekord
		$result = \Mmi\Orm\DbConnector::getAdapter()->select('MAX(' . \Mmi\Orm\DbConnector::getAdapter()->prepareField($keyName) . ')', $this->_prepareFrom(), $this->_query->getQueryCompile()->where, $this->_query->getQueryCompile()->groupBy, $this->_query->getQueryCompile()->order, 1, null, $this->_query->getQueryCompile()->bind);
		return isset($result[0]) ? current($result[0]) : null;
	}

	/**
	 * Pobiera wartość minimalną z kolumny
	 * @param string $keyName nazwa klucza
	 * @return string wartość minimalna
	 */
	public final function findMin($keyName) {
		//odpytanie adaptera o rekord
		$result = \Mmi\Orm\DbConnector::getAdapter()->select('MIN(' . \Mmi\Orm\DbConnector::getAdapter()->prepareField($keyName) . ')', $this->_prepareFrom(), $this->_query->getQueryCompile()->where, $this->_query->getQueryCompile()->groupBy, $this->_query->getQueryCompile()->order, 1, null, $this->_query->getQueryCompile()->bind);
		return isset($result[0]) ? current($result[0]) : null;
	}

	/**
	 * Pobiera sumę z kolumny
	 * @param string $keyName nazwa klucza
	 * @return string wartość minimalna
	 */
	public final function findSum($keyName) {
		//odpytanie adaptera o rekord
		$result = \Mmi\Orm\DbConnector::getAdapter()->select('SUM(' . \Mmi\Orm\DbConnector::getAdapter()->prepareField($keyName) . ')', $this->_prepareFrom(), $this->_query->getQueryCompile()->where, $this->_query->getQueryCompile()->groupBy, $this->_query->getQueryCompile()->order, 1, null, $this->_query->getQueryCompile()->bind);
		return isset($result[0]) ? current($result[0]) : null;
	}

	/**
	 * Pobiera unikalne wartości kolumny
	 * @param string $keyName nazwa klucza
	 * @return array mixed wartości unikalne
	 */
	public final function findUnique($keyName) {
		//odpytanie adaptera o rekordy
		$data = \Mmi\Orm\DbConnector::getAdapter()->select('DISTINCT(' . \Mmi\Orm\DbConnector::getAdapter()->prepareField($keyName) . ')', $this->_prepareFrom(), $this->_query->getQueryCompile()->where, $this->_query->getQueryCompile()->groupBy, $this->_query->getQueryCompile()->order, null, null, $this->_query->getQueryCompile()->bind);
		$result = [];
		//dodaje kolejne wartości do tablicy
		foreach ($data as $line) {
			$result[] = current($line);
		}
		return $result;
	}

	/**
	 * Przygotowuje pola do selecta
	 * @return string
	 */
	protected final function _prepareFields() {
		//jeśli pusty schemat połączeń
		if (empty($this->_query->getQueryCompile()->joinSchema)) {
			return '*';
		}
		$fields = '';
		//pobranie struktury tabeli
		$mainStructure = \Mmi\Orm\DbConnector::getTableStructure($this->_query->getTableName());
		$table = \Mmi\Orm\DbConnector::getAdapter()->prepareTable($this->_query->getTableName());
		//pola z tabeli głównej
		foreach ($mainStructure as $fieldName => $info) {
			$fields .= $table . '.' . \Mmi\Orm\DbConnector::getAdapter()->prepareField($fieldName) . ', ';
		}
		//pola z tabel dołączonych
		foreach ($this->_query->getQueryCompile()->joinSchema as $schema) {
			//pobranie struktury tabeli dołączonej
			$structure = \Mmi\Orm\DbConnector::getTableStructure($schema[0]);
			$joinAlias = isset($schema[5]) ? $schema[5] : $schema[0];
			//pola tabeli dołączonej
			foreach ($structure as $fieldName => $info) {
				$fields .= \Mmi\Orm\DbConnector::getAdapter()->prepareTable($joinAlias) . '.' . \Mmi\Orm\DbConnector::getAdapter()->prepareField($fieldName) . ' AS ' . \Mmi\Orm\DbConnector::getAdapter()->prepareField($joinAlias . '__' . $schema[0] . '__' . $fieldName) . ', ';
			}
		}
		return rtrim($fields, ', ');
	}

	/**
	 * Przygotowuje sekcję FROM
	 * @return string
	 */
	protected final function _prepareFrom() {
		$table = \Mmi\Orm\DbConnector::getAdapter()->prepareTable($this->_query->getTableName());
		//jeśli brak joinów sama tabela
		if (empty($this->_query->getQueryCompile()->joinSchema)) {
			return $table;
		}
		$baseTable = $table;
		//przygotowanie joinów
		foreach ($this->_query->getQueryCompile()->joinSchema as $schema) {
			$targetTable = isset($schema[3]) ? $schema[3] : $baseTable;
			$joinType = isset($schema[4]) ? $schema[4] : 'JOIN';
			$joinAlias = isset($schema[5]) ? $schema[5] : $schema[0];
			$table .= ' ' . $joinType . ' ' . \Mmi\Orm\DbConnector::getAdapter()->prepareTable($schema[0]) . ' AS ' . \Mmi\Orm\DbConnector::getAdapter()->prepareTable($joinAlias) . ' ON ' .
				\Mmi\Orm\DbConnector::getAdapter()->prepareTable($joinAlias) . '.' . \Mmi\Orm\DbConnector::getAdapter()->prepareField($schema[1]) .
				' = ' . \Mmi\Orm\DbConnector::getAdapter()->prepareTable($targetTable) . '.' . \Mmi\Orm\DbConnector::getAdapter()->prepareField($schema[2]);
		}
		return $table;
	}

}

<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Orm\Query;

class Field {

	/**
	 * Nazwa pola
	 * @var string
	 */
	protected $_fieldName;

	/**
	 * Kwantyfikator łączenia AND lub OR
	 * @var string
	 */
	protected $_logic;

	/**
	 * Referencja do nadrzędnego zapytania
	 * @var \Mmi\Orm\Query
	 */
	protected $_query;

	/**
	 * Ustawia parametry pola
	 * @param \Mmi\Orm\Query $query zapytanie nadrzędne
	 * @param string $fieldName nazwa pola
	 * @param string $logic kwantyfikator łączenia AND lub OR
	 */
	public function __construct(\Mmi\Orm\Query $query, $fieldName, $logic = 'AND') {
		$this->_fieldName = $fieldName;
		$this->_logic = ($logic == 'OR') ? 'OR' : 'AND';
		$this->_query = $query;
	}
	
	/**
	 * Magiczne wywołanie metod equalsColumn, greaterThanColumn itp.
	 * @param string $name
	 * @param array $params
	 * @return \Mmi\Orm\Query
	 * @throws \Mmi\Orm\Exception
	 */
	public final function __call($name, $params) {
		//znajdowanie 2 podciągów: 1 - nazwa metody, 2 - wartość pola
		if (!preg_match('/(equalsColumn|notEqualsColumn|greaterThanColumn|lessThanColumn|greaterOrEqualsColumn|lessOrEqualsColumn)([a-zA-Z0-9]+)/', $name, $matches) || !empty($params)) {
			//brak metody pasującej do wzorca
			throw new \Mmi\Orm\Exception('Method not found ' . $name);
		}
		//wywołanie metody
		return $this->{$matches[1]}(lcfirst($matches[2]));
	}

	/**
	 * Równość
	 * @param mixed $value
	 * @return \Mmi\Orm\Query
	 */
	public function equals($value) {
		return $this->_prepareQuery($value, '=');
	}

	/**
	 * Równość kolumn
	 * @param string $columnName
	 * @param string $tableName
	 * @return \Mmi\Orm\Query
	 */
	public function equalsColumn($columnName, $tableName = null) {
		return $this->_prepareColumnQuery($columnName, $tableName, '=');
	}

	/**
	 * Negacja równości
	 * @param mixed $value
	 * @return \Mmi\Orm\Query
	 */
	public function notEquals($value) {
		return $this->_prepareQuery($value, '<>');
	}

	/**
	 * Negacja równości kolumn
	 * @param string $columnName
	 * @param string $tableName
	 * @return \Mmi\Orm\Query
	 */
	public function notEqualsColumn($columnName, $tableName = null) {
		return $this->_prepareColumnQuery($columnName, $tableName, '<>');
	}

	/**
	 * Relacja większości
	 * @param mixed $value
	 * @return \Mmi\Orm\Query
	 */
	public function greater($value) {
		return $this->_prepareQuery($value, '>');
	}

	/**
	 * Relacja większości kolumny nad inną
	 * @param string $columnName
	 * @param string $tableName
	 * @return \Mmi\Orm\Query
	 */
	public function greaterThanColumn($columnName, $tableName = null) {
		return $this->_prepareColumnQuery($columnName, $tableName, '>');
	}

	/**
	 * Relacja mniejszości
	 * @param mixed $value
	 * @return \Mmi\Orm\Query
	 */
	public function less($value) {
		return $this->_prepareQuery($value, '<');
	}

	/**
	 * Relacja mniejszości kolumny do innej
	 * @param string $columnName
	 * @param string $tableName
	 * @return \Mmi\Orm\Query
	 */
	public function lessThanColumn($columnName, $tableName = null) {
		return $this->_prepareColumnQuery($columnName, $tableName, '<');
	}

	/**
	 * Relacja większe-równe
	 * @param mixed $value
	 * @return \Mmi\Orm\Query
	 */
	public function greaterOrEquals($value) {
		return $this->_prepareQuery($value, '>=');
	}

	/**
	 * Relacja większości lub równości kolumny z inną
	 * @param string $columnName
	 * @param string $tableName
	 * @return \Mmi\Orm\Query
	 */
	public function greaterOrEqualsColumn($columnName, $tableName = null) {
		return $this->_prepareColumnQuery($columnName, $tableName, '>=');
	}

	/**
	 * Relacja mniejsze-równe
	 * @param type $value
	 * @return \Mmi\Orm\Query
	 */
	public function lessOrEquals($value) {
		return $this->_prepareQuery($value, '<=');
	}

	/**
	 * Relacja mniejszości lub równości kolumny z inną
	 * @param string $columnName
	 * @param string $tableName
	 * @return \Mmi\Orm\Query
	 */
	public function lessOrEqualsColumn($columnName, $tableName = null) {
		return $this->_prepareColumnQuery($columnName, $tableName, '<=');
	}

	/**
	 * Porównanie podobieństwa
	 * @param string $value
	 * @return \Mmi\Orm\Query
	 */
	public function like($value) {
		return $this->_prepareQuery($value, 'LIKE');
	}

	/**
	 * Porównanie podobieństwa bez wielkości liter
	 * @param string $value
	 * @return \Mmi\Orm\Query
	 */
	public function ilike($value) {
		return $this->_prepareQuery($value, 'ILIKE');
	}

	/**
	 * Przygotowuje zapytanie
	 * @param mixed $value
	 * @param string $condition
	 * @return \Mmi\Orm\Query
	 */
	protected function _prepareQuery($value, $condition = '=') {
		//tworzenie binda
		$bindKey = \Mmi\Db\Adapter\Pdo\PdoAbstract::generateBindKey();
		//wartość nie null i nie tabelaryczna
		if (!is_array($value) && null !== $value) {
			$this->_query->getQueryCompile()->bind[$bindKey] = $value;
		}
		//inicjalizacja zapytania
		$this->_initQuery();
		//przygotowanie wartości null
		if (null === $value) {
			$this->_query->getQueryCompile()->where .= \Mmi\Orm\DbConnector::getAdapter()->prepareNullCheck($this->_fieldName, ($condition == '='));
			return $this->_query;
		}
		//przygotowanie pustych tabel (kompatybilne tylko z == i <>)
		if (is_array($value) && empty($value)) {
			//jeśli tabela jest pusta i negacja to 1 jeśli równość to 0
			$this->_query->getQueryCompile()->where .= $condition == '<>' ? 1 : 0;
			return $this->_query;
		}
		//przygotowanie typów tabelarycznych
		if (is_array($value)) {
			$fields = '';
			//bindowanie parametrów
			foreach ($value as $arg) {
				$bk = \Mmi\Db\Adapter\Pdo\PdoAbstract::generateBindKey();
				$this->_query->getQueryCompile()->bind[$bk] = $arg;
				$fields .= ':' . $bk . ', ';
			}
			//dodawanie IN do where
			$this->_query->getQueryCompile()->where .= $this->_fieldName . ' ' . ($condition == '<>' ? 'NOT IN' : 'IN') . '(' . trim($fields, ', ') . ')';
			return $this->_query;
		}
		//ilike
		if ('ILIKE' == $condition) {
			$this->_query->getQueryCompile()->where .= \Mmi\Orm\DbConnector::getAdapter()->prepareIlike($this->_fieldName) . ' :' . $bindKey;
			return $this->_query;
		}
		//zwykłe porównanie
		$this->_query->getQueryCompile()->where .= $this->_fieldName . ' ' . $condition . ' :' . $bindKey;
		return $this->_query;
	}

	/**
	 * Przygotowuje zapytanie
	 * @param mixed $value
	 * @param string $condition
	 * @return \Mmi\Orm\Query
	 */
	protected function _prepareColumnQuery($columnName, $tableName = null, $condition = '=') {
		//inicjalizacja zapytania
		$this->_initQuery();
		//porównanie z kolumną
		$this->_query->getQueryCompile()->where .= $this->_fieldName . ' ' . $condition . ' ' . \Mmi\Orm\DbConnector::getAdapter()->prepareTable((null === $tableName) ? $this->_query->getTableName() : $tableName) . '.' . \Mmi\Orm\DbConnector::getAdapter()->prepareField($columnName);
		return $this->_query;
	}

	/**
	 * Inicjalizacja zapytania
	 */
	protected function _initQuery() {
		//pusty where - inicjalizacja
		if ($this->_query->getQueryCompile()->where == '') {
			return $this->_query->getQueryCompile()->where = 'WHERE ';
		}
		//dodawanie operatora logicznego
		$this->_query->getQueryCompile()->where .= ' ' . $this->_logic . ' ';
	}

}

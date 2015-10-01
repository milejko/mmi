<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Orm;

/**
 * Klasa zapytania powoływana przez Query::factory()
 * umożliwia odpytywanie DAO o Rekordy
 */
class Query {

	/**
	 * Kompilant zapytania
	 * @var \Mmi\Orm\QueryCompile
	 */
	protected $_compile;

	/**
	 * Nazwa klasy DAO
	 * @var string
	 */
	protected $_tableName;

	/**
	 * Konstruktor tworzy nowe skompilowane zapytanie
	 * @throws \Mmi\Orm\OrmException tabela niewyspecyfikowana
	 * @param string $tableName nazwa tabeli
	 */
	protected final function __construct($tableName = null) {
		//nowa kompilacja
		$this->_compile = new \Mmi\Orm\QueryCompile();
		//klasa DAO na podstawie parametru konstruktora
		if ($tableName !== null) {
			$this->_tableName = $tableName;
			return;
		}
		//jeśli ustalona klasa - wyjście
		if ($this->_tableName === null) {
			throw new OrmException('Table name not specified');
		}
	}

	/**
	 * Magiczne wywołanie metod where, order itd.
	 * @param string $name
	 * @param array $params
	 * @return \Mmi\Orm\Query
	 * @throws \Mmi\Orm\OrmException
	 */
	public final function __call($name, $params) {
		//znajdowanie 2 podciągów: 1 - nazwa metody, 2 - wartość pola
		if (!preg_match('/(where|andField|orField|orderAsc|orderDesc|groupBy)([a-zA-Z0-9]+)/', $name, $matches) || !empty($params)) {
			//brak metody pasującej do wzorca
			throw new OrmException('Method not found ' . $name);
		}
		//wywołanie metody
		return $this->{$matches[1]}(lcfirst($matches[2]));
	}

	/**
	 * Zwraca instancję siebie
	 * @return \Mmi\Orm\Query
	 */
	public static function factory($tableName = null) {
		//nowy obiekt swojej klasy
		return new self($tableName);
	}

	/**
	 * Ustawia limit
	 * @param int $limit
	 * @return \Mmi\Orm\Query
	 */
	public final function limit($limit = null) {
		$this->_compile->limit = $limit;
		return $this;
	}

	/**
	 * Ustawia ofset
	 * @param int $offset
	 * @return \Mmi\Orm\Query
	 */
	public final function offset($offset = null) {
		$this->_compile->offset = $offset;
		return $this;
	}

	/**
	 * Sortowanie rosnące
	 * @param string $fieldName nazwa pola
	 * @param string $tableName opcjonalna nazwa tabeli źródłowej
	 * @return \Mmi\Orm\Query
	 */
	public final function orderAsc($fieldName, $tableName = null) {
		return $this->_prepareOrder($fieldName, $tableName);
	}

	/**
	 * Sortowanie malejące
	 * @param string $fieldName nazwa pola
	 * @param string $tableName opcjonalna nazwa tabeli źródłowej
	 * @return \Mmi\Orm\Query
	 */
	public final function orderDesc($fieldName, $tableName = null) {
		return $this->_prepareOrder($fieldName, $tableName, false);
	}

	/**
	 * Grupowanie
	 * @param string $fieldName
	 * @param string $tableName
	 * @return \Mmi\Orm\Query
	 */
	public final function groupBy($fieldName, $tableName = null) {
		return $this->_prepareGroup($fieldName, $tableName);
	}

	/**
	 * Dodaje podsekcję AND
	 * @param \Mmi\Orm\Query $query
	 * @return \Mmi\Orm\Query
	 */
	public final function andQuery(\Mmi\Orm\Query $query) {
		return $this->_mergeQueries($query, true);
	}

	/**
	 * Dodaje podsekcję WHERE (jak AND)
	 * @param \Mmi\Orm\Query $query
	 * @return \Mmi\Orm\Query
	 */
	public final function whereQuery(\Mmi\Orm\Query $query) {
		//jest aliasem na metodę andQuery()
		return $this->andQuery($query);
	}

	/**
	 * Dodaje podsekcję OR
	 * @param \Mmi\Orm\Query $query
	 * @return \Mmi\Orm\Query
	 */
	public final function orQuery(\Mmi\Orm\Query $query) {
		return $this->_mergeQueries($query, false);
	}

	/**
	 * Dodaje warunek na pole AND
	 * @param string $fieldName nazwa pola
	 * @param string $tableName opcjonalna nazwa tabeli źródłowej
	 * @return QueryHelper\QueryField
	 */
	public final function andField($fieldName, $tableName = null) {
		return new QueryHelper\QueryField($this, $this->_prepareField($fieldName, $tableName), 'AND');
	}

	/**
	 * Pierwszy warunek w zapytaniu (domyślnie AND)
	 * @param string $fieldName nazwa pola
	 * @param string $tableName opcjonalna nazwa tabeli źródłowej
	 * @return QueryHelper\QueryField
	 */
	public final function where($fieldName, $tableName = null) {
		return $this->andField($fieldName, $tableName);
	}

	/**
	 * Dodaje warunek na pole OR
	 * @param string $fieldName nazwa pola
	 * @param string $tableName opcjonalna nazwa tabeli źródłowej
	 * @return QueryHelper\QueryField
	 */
	public final function orField($fieldName, $tableName = null) {
		return new QueryHelper\QueryField($this, $this->_prepareField($fieldName, $tableName), 'OR');
	}

	/**
	 * Dołącza tabelę tabelę
	 * @param string $tableName nazwa tabeli
	 * @param string $targetTableName opcjonalnie nazwa tabeli do której łączyć
	 * @return \Mmi\Orm\QueryHelper\QueryJoin
	 */
	public final function join($tableName, $targetTableName = null) {
		return new \Mmi\Orm\QueryHelper\QueryJoin($this, $tableName, 'JOIN', $targetTableName);
	}

	/**
	 * Dołącza tabelę złączeniem lewym
	 * @param string $tableName nazwa tabeli
	 * @param string $targetTableName opcjonalnie nazwa tabeli do której łączyć
	 * @return \Mmi\Orm\QueryHelper\QueryJoin
	 */
	public final function joinLeft($tableName, $targetTableName = null) {
		return new \Mmi\Orm\QueryHelper\QueryJoin($this, $tableName, 'LEFT JOIN', $targetTableName);
	}

	/**
	 * Zwraca skompilowane zapytanie
	 * @return \Mmi\Orm\QueryCompile
	 */
	public final function getQueryCompile() {
		return $this->_compile;
	}

	/**
	 * Zwraca skrót MD5 zapytania
	 * @return string
	 */
	public final function getQueryCompileHash() {
		return md5(str_replace(array_keys($this->_compile->bind), array_values($this->_compile->bind), print_r($this->_compile, true)));
	}

	/**
	 * Zwraca nazwę klasy DAO
	 * @return string
	 */
	public final function getTableName() {
		return $this->_tableName;
	}

	/**
	 * Zwraca nazwę klasy rekordu
	 * @return string
	 */
	public final function getRecordName() {
		//konwencja nazwy na rekord
		return self::_classPrefix() . 'Record';
	}

	/**
	 * Zwraca nazwę klasy zapytania
	 * @return string
	 */
	public static final function getCollectionName() {
		return '\Mmi\Orm\RecordCollection';
		//konwencja nazwy na kolekcję
		//return self::_classPrefix() . 'Record\Collection';
	}

	/**
	 * Resetuje sortowanie w zapytaniu
	 * @return \Mmi\Orm\Query
	 */
	public final function resetOrder() {
		$this->_compile->order = '';
		return $this;
	}

	/**
	 * Resetuje grupowanie
	 * @return \Mmi\Orm\Query
	 */
	public final function resetGroupBy() {
		$this->_compile->groupBy = '';
		return $this;
	}

	/**
	 * Resetuje warunki w zapytaniu
	 * @return \Mmi\Orm\Query
	 */
	public final function resetWhere() {
		//czyszczenie zapytania
		$this->_compile->where = '';
		//usuwanie powiązane zmienne
		$this->_compile->bind = [];
		return $this;
	}

	/**
	 * Pobiera ilość rekordów
	 * @return int
	 */
	public final function count($column = '*') {
		return QueryData::factory($this)
				->count($column);
	}

	/**
	 * Pobiera pierwszy rekord po kluczu głównym ID
	 * null jeśli brak danych
	 * @param int $id
	 * @return \Mmi\Orm\Record
	 */
	public final function findPk($id) {
		//zwróci null jeśli brak danych
		return $this->where('id')->equals($id)
				->findFirst();
	}

	/**
	 * Pobiera wszystkie rekordy i zwraca ich kolekcję
	 * @return \Mmi\Orm\RecordCollection
	 */
	public final function find() {
		return QueryData::factory($this)
				->find();
	}

	/**
	 * Pobiera obiekt pierwszy ze zbioru
	 * null jeśli brak danych
	 * @param \Mmi\Orm\Query $q Obiekt zapytania
	 * @return \Mmi\Orm\RecordRo
	 */
	public final function findFirst() {
		return QueryData::factory($this)
				->findFirst();
	}

	/**
	 * Zwraca tablicę asocjacyjną (pary)
	 * @param string $keyName
	 * @param string $valueName
	 * @return array
	 */
	public final function findPairs($keyName, $valueName) {
		return QueryData::factory($this)
				->findPairs($keyName, $valueName);
	}

	/**
	 * Pobiera wartość maksymalną z kolumny
	 * @param string $keyName nazwa klucza
	 * @return string wartość maksymalna
	 */
	public final function findMax($keyName) {
		return QueryData::factory($this)
				->findMax($keyName);
	}

	/**
	 * Pobiera wartość minimalną z kolumny
	 * @param string $keyName nazwa klucza
	 * @return string wartość minimalna
	 */
	public final function findMin($keyName) {
		return QueryData::factory($this)
				->findMin($keyName);
	}

	/**
	 * Pobiera unikalne wartości kolumny
	 * @param string $keyName nazwa klucza
	 * @return array mixed wartości unikalne
	 */
	public final function findUnique($keyName) {
		return QueryData::factory($this)
				->findUnique($keyName);
	}

	/**
	 * Łączy query
	 * @param boolean $type
	 * @return \Mmi\Orm\Query
	 */
	protected final function _mergeQueries(\Mmi\Orm\Query $query, $and = true) {
		$compilation = $query->getQueryCompile();
		//łączenie where
		if ($compilation->where) {
			$connector = $this->_compile->where ? ($and ? ' AND (' : ' OR (') : 'WHERE (';
			$this->_compile->where .= $connector . substr($compilation->where, 6) . ')';
		}
		//łączenie wartości
		if (!empty($compilation->bind)) {
			$this->_compile->bind = array_merge($compilation->bind, $this->_compile->bind);
		}
		//suma joinów query nadrzędnej i podrzędnej
		if (!empty($compilation->joinSchema)) {
			$this->_compile->joinSchema = array_merge($this->_compile->joinSchema, $compilation->joinSchema);
		}
		//łączenie order
		if ($compilation->order) {
			if (substr($this->_compile->order, 0, 8) == 'ORDER BY' && substr($compilation->order, 0, 8) == 'ORDER BY') {
				$this->_compile->order .= ', ' . substr($compilation->order, 9);
			} else {
				$this->_compile->order .= $compilation->order;
			}
		}
		return $this;
	}

	/**
	 * Przygotowuje nazwę pola do zapytania, konwertuje camelcase na podkreślenia
	 * @param string $fieldName
	 * @param string $forcedTableName
	 * @return string
	 * @throws \Mmi\Orm\OrmException
	 */
	protected final function _prepareField($fieldName, $forcedTableName = null) {
		$tableName = ($forcedTableName === null) ? $this->_tableName : $forcedTableName;
		//tabela
		$tablePrefix = \Mmi\Orm\DbConnector::getAdapter()->prepareTable($tableName);
		//jeśli pole występuje w tabeli, bądź jest funkcją RAND()
		if (\Mmi\Orm\DbConnector::fieldInTable($fieldName, $tableName) || $fieldName == 'RAND()') {
			return $tablePrefix . '.' . \Mmi\Orm\DbConnector::getAdapter()->prepareField($fieldName);
		}
		/* @var $db \Mmi\Db\Adapter\Pdo\PdoAbstract */
		//konwersja camelcase do podkreślników (przechowywanych w bazie)
		$convertedFieldName = \Mmi\Orm\Convert::camelcaseToUnderscore($fieldName);
		//jeśli pole podkreślnikowe występuje w bazie
		if (\Mmi\Orm\DbConnector::fieldInTable($convertedFieldName, $tableName)) {
			return $tablePrefix . '.' . \Mmi\Orm\DbConnector::getAdapter()->prepareField($convertedFieldName);
		}
		//w pozostałych wypadkach wyjątek o braku pola
		throw new OrmException(get_called_class() . ': "' . $fieldName . '" not found in ' . $tableName . ' table');
	}

	/**
	 * Przygotowuje order
	 * @param string $fieldName
	 * @param string $tableName
	 * @param boolean $asc
	 * @return \Mmi\Orm\Query
	 */
	protected final function _prepareOrder($fieldName, $tableName = null, $asc = true) {
		//jeśli pusty order - dodawanie ORDER BY na początku
		if (!$this->_compile->order) {
			$this->_compile->order = 'ORDER BY ';
		} else {
			$this->_compile->order .= ', ';
		}
		$this->_compile->order .= $this->_prepareField($fieldName, $tableName) . ' ' . ($asc ? 'ASC' : 'DESC');
		return $this;
	}

	/**
	 * Przygotowanie grupowanie
	 * @param type $fieldName
	 * @param type $tableName
	 * @return \Mmi\Orm\Query
	 */
	protected final function _prepareGroup($fieldName, $tableName = null) {
		//jeśli pusty groupby - dodawanie GROUP BY na początku
		if (!$this->_compile->groupBy) {
			$this->_compile->groupBy = 'GROUP BY ';
		} else {
			$this->_compile->groupBy .= ', ';
		}
		$this->_compile->groupBy .= $this->_prepareField($fieldName, $tableName);
		return $this;
	}

	/**
	 * Prefix klasy dla rekordów kolekcji i quer
	 * @return string
	 */
	protected static function _classPrefix() {
		return substr(get_called_class(), 0, -5);
	}

}

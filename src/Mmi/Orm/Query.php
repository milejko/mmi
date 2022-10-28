<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Orm;

use Mmi\App\App;
use Mmi\Db\DbInformationInterface;
use Mmi\Db\DbInterface;

/**
 * Klasa zapytania
 * umożliwia odpytywanie DAO o Rekordy
 */
class Query
{
    /**
     * Kompilant zapytania
     * @var QueryCompile
     */
    protected $_compile;

    /**
     * Nazwa klasy DAO
     * @var string
     */
    protected $_tableName;

    /**
     * @var DbInterface
     */
    protected $db;

    /**
     * @var DbInformationInterface
     */
    protected $dbInformation;

    /**
     * Konstruktor tworzy nowe skompilowane zapytanie
     * @throws \Mmi\Orm\OrmException tabela niewyspecyfikowana
     * @param string $tableName nazwa tabeli
     */
    final public function __construct($tableName = null)
    {
        //@TODO: proper DI (could be impossible)
        $this->db            = App::$di->get(DbInterface::class);
        $this->dbInformation = App::$di->get(DbInformationInterface::class);
        //nowa kompilacja
        $this->_compile = new QueryCompile();
        //klasa DAO na podstawie parametru konstruktora
        if ($tableName) {
            $this->_tableName = $tableName;
        }
    }

    /**
     * Magiczne wywołanie metod where, order itd.
     * @param string $name
     * @param array $params
     * @return Query
     * @throws OrmException
     */
    final public function __call($name, $params)
    {
        //znajdowanie 2 podciągów: 1 - nazwa metody, 2 - wartość pola
        if (!preg_match('/(where|andField|orField|orderAsc|orderDesc|groupBy)([a-zA-Z0-9]+)/', $name, $matches) || !empty($params)) {
            //brak metody pasującej do wzorca
            throw new OrmException('Method not found ' . $name);
        }
        //wywołanie metody
        return $this->{$matches[1]}(lcfirst($matches[2]));
    }

    /**
     * Ustawia limit
     * @param int $limit
     * @return Query
     */
    final public function limit($limit = null)
    {
        $this->_compile->limit = $limit;
        return $this;
    }

    /**
     * Ustawia ofset
     * @param int $offset
     * @return Query
     */
    final public function offset($offset = null)
    {
        $this->_compile->offset = $offset;
        return $this;
    }

    /**
     * Sortowanie rosnące
     * @param string $fieldName nazwa pola
     * @param string $tableName opcjonalna nazwa tabeli źródłowej
     * @return Query
     */
    final public function orderAsc($fieldName, $tableName = null)
    {
        return $this->_prepareOrder($fieldName, $tableName);
    }

    /**
     * Sortowanie malejące
     * @param string $fieldName nazwa pola
     * @param string $tableName opcjonalna nazwa tabeli źródłowej
     * @return Query
     */
    final public function orderDesc($fieldName, $tableName = null)
    {
        return $this->_prepareOrder($fieldName, $tableName, false);
    }

    /**
     * Grupowanie
     * @param string $fieldName
     * @param string $tableName
     * @return Query
     */
    final public function groupBy($fieldName, $tableName = null)
    {
        return $this->_prepareGroup($fieldName, $tableName);
    }

    /**
     * Dodaje podsekcję AND
     * @param Query $query
     * @return Query
     */
    final public function andQuery(Query $query)
    {
        return $this->_mergeQueries($query, true);
    }

    /**
     * Dodaje podsekcję WHERE (jak AND)
     * @param Query $query
     * @return Query
     */
    final public function whereQuery(Query $query)
    {
        //jest aliasem na metodę andQuery()
        return $this->andQuery($query);
    }

    /**
     * Dodaje podsekcję OR
     * @param Query $query
     * @return Query
     */
    final public function orQuery(Query $query)
    {
        return $this->_mergeQueries($query, false);
    }

    /**
     * Dodaje warunek na pole AND
     * @param string $fieldName nazwa pola
     * @param string $tableName opcjonalna nazwa tabeli źródłowej
     * @return QueryHelper\QueryField
     */
    final public function andField($fieldName, $tableName = null)
    {
        return new QueryHelper\QueryField($this, $this->_prepareField($fieldName, $tableName), 'AND');
    }

    /**
     * Pierwszy warunek w zapytaniu (domyślnie AND)
     * @param string $fieldName nazwa pola
     * @param string $tableName opcjonalna nazwa tabeli źródłowej
     * @return QueryHelper\QueryField
     */
    final public function where($fieldName, $tableName = null)
    {
        return $this->andField($fieldName, $tableName);
    }

    /**
     * Dodaje warunek na pole OR
     * @param string $fieldName nazwa pola
     * @param string $tableName opcjonalna nazwa tabeli źródłowej
     * @return QueryHelper\QueryField
     */
    final public function orField($fieldName, $tableName = null)
    {
        return new QueryHelper\QueryField($this, $this->_prepareField($fieldName, $tableName), 'OR');
    }

    /**
     * Dołącza tabelę tabelę
     * @param string $tableName nazwa tabeli
     * @param string $targetTableName opcjonalnie nazwa tabeli do której łączyć
     * @param string $alias alias złączenia
     * @return QueryHelper\QueryJoin
     */
    final public function join($tableName, $targetTableName = null, $alias = null)
    {
        return new QueryHelper\QueryJoin($this, $tableName, 'JOIN', $targetTableName, $alias);
    }

    /**
     * Dołącza tabelę złączeniem lewym
     * @param string $tableName nazwa tabeli
     * @param string $targetTableName opcjonalnie nazwa tabeli do której łączyć
     * @param string $alias alias złączenia
     * @return QueryHelper\QueryJoin
     */
    final public function joinLeft($tableName, $targetTableName = null, $alias = null)
    {
        return new QueryHelper\QueryJoin($this, $tableName, 'LEFT JOIN', $targetTableName, $alias);
    }

    /**
     * Zwraca skompilowane zapytanie
     * @return QueryCompile
     */
    final public function getQueryCompile()
    {
        return $this->_compile;
    }

    /**
     * Zwraca skrót MD5 zapytania
     * @return string
     */
    final public function getQueryCompileHash()
    {
        return md5(str_replace(array_keys($this->_compile->bind), array_values($this->_compile->bind), print_r($this->_compile, true)));
    }

    /**
     * Zwraca nazwę klasy DAO
     * @return string
     */
    final public function getTableName()
    {
        return $this->_tableName;
    }

    /**
     * Zwraca nazwę klasy rekordu
     * @return string
     */
    final public function getRecordName()
    {
        //konwencja nazwy na rekord
        return self::_classPrefix() . 'Record';
    }

    /**
     * Zwraca nazwę klasy zapytania
     * @return string
     */
    final public static function getCollectionName()
    {
        return '\Mmi\Orm\RecordCollection';
        //konwencja nazwy na kolekcję
        //return self::_classPrefix() . 'RecordCollection';
    }

    /**
     * Resetuje sortowanie w zapytaniu
     * @return Query
     */
    final public function resetOrder()
    {
        $this->_compile->order = '';
        return $this;
    }

    /**
     * Resetuje grupowanie
     * @return Query
     */
    final public function resetGroupBy()
    {
        $this->_compile->groupBy = '';
        return $this;
    }

    /**
     * Resetuje warunki w zapytaniu
     * @return Query
     */
    final public function resetWhere()
    {
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
    final public function count($column = '*')
    {
        return (new QueryData($this))->count($column);
    }

    /**
     * Delete complete collection
     */
    final public function delete()
    {
        //iterate records
        foreach ((new QueryData($this))->find() as $record) {
            $record->delete();
        }
    }

    /**
     * Pobiera pierwszy rekord po kluczu głównym ID
     * null jeśli brak danych
     * @param int $id
     * @return Record
     */
    final public function findPk($id)
    {
        //zwróci null jeśli brak danych
        return $this->where('id')->equals($id)->findFirst();
    }

    /**
     * Pobiera obiekt pierwszy ze zbioru
     * null jeśli brak danych
     * @param Query $q Obiekt zapytania
     * @return RecordRo
     */
    final public function findFirst()
    {
        return (new QueryData($this))->findFirst();
    }

    /**
     * Pobiera wszystkie rekordy i zwraca ich kolekcję
     * @return RecordCollection
     */
    final public function find()
    {
        return (new QueryData($this))->find();
    }

    /**
     * Pobiera wybrane pola w postaci tabeli
     * @return array
     */
    final public function findFields(array $fields)
    {
        return (new QueryData($this))->findFields($fields);
    }

    /**
     * Zwraca tablicę asocjacyjną (pary)
     * @param string $keyName
     * @param string $valueName
     * @return array
     */
    final public function findPairs($keyName, $valueName)
    {
        return (new QueryData($this))->findPairs($keyName, $valueName);
    }

    /**
     * Pobiera wartość maksymalną z kolumny
     * @param string $keyName nazwa klucza
     * @return string wartość maksymalna
     */
    final public function findMax($keyName)
    {
        return (new QueryData($this))->findMax($keyName);
    }

    /**
     * Pobiera wartość minimalną z kolumny
     * @param string $keyName nazwa klucza
     * @return string wartość minimalna
     */
    final public function findMin($keyName)
    {
        return (new QueryData($this))->findMin($keyName);
    }

    /**
     * Pobiera sumę z kolumny
     * @param string $keyName nazwa klucza
     * @return string suma
     */
    final public function findSum($keyName)
    {
        return (new QueryData($this))->findSum($keyName);
    }

    /**
     * Pobiera unikalne wartości kolumny
     * @param string $keyName nazwa klucza
     * @return array mixed wartości unikalne
     */
    final public function findUnique($keyName)
    {
        return (new QueryData($this))->findUnique($keyName);
    }

    /**
     * Łączy query
     * @param boolean $type
     * @return Query
     */
    final protected function _mergeQueries(Query $query, $and = true)
    {
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
     * @throws OrmException
     */
    final protected function _prepareField($fieldName, $forcedTableName = null)
    {
        //funkcja sortująca rand()
        if ($fieldName == 'RAND()') {
            return $this->db->prepareField('RAND()');
        }
        //nazwa tabeli
        $tableName = ($forcedTableName === null) ? $this->_tableName : $forcedTableName;
        //przygotowany prefix tabeli
        $tablePrefix = $this->db->prepareTable($tableName);
        //pole wymaga konwersji do underscore
        if ($this->dbInformation->isTableContainsField($tableName, $underscoreFieldName = Convert::camelcaseToUnderscore($fieldName))) {
            return $tablePrefix . '.' . $underscoreFieldName;
        }
        //konwersja camelcase na podkreślenia
        return $tablePrefix . '.' . $this->db->prepareField($fieldName);
    }

    /**
     * Przygotowuje order
     * @param string $fieldName
     * @param string $tableName
     * @param boolean $asc
     * @return Query
     */
    final protected function _prepareOrder($fieldName, $tableName = null, $asc = true)
    {
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
     * @return Query
     */
    final protected function _prepareGroup($fieldName, $tableName = null)
    {
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
    protected static function _classPrefix()
    {
        return substr(get_called_class(), 0, -5);
    }
}

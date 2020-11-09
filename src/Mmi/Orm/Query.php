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
    public final function __construct($tableName = null)
    {
        //@TODO: proper DI (could be impossible)
        $this->db            = App::$di->get(DbInterface::class);
        $this->dbInformation = App::$di->get(DbInformationInterface::class);
        //nowa kompilacja
        $this->_compile = new QueryCompile;
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
    public final function __call($name, $params)
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
    public final function limit($limit = null)
    {
        $this->_compile->limit = $limit;
        return $this;
    }

    /**
     * Ustawia ofset
     * @param int $offset
     * @return Query
     */
    public final function offset($offset = null)
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
    public final function orderAsc($fieldName, $tableName = null)
    {
        return $this->_prepareOrder($fieldName, $tableName);
    }

    /**
     * Sortowanie malejące
     * @param string $fieldName nazwa pola
     * @param string $tableName opcjonalna nazwa tabeli źródłowej
     * @return Query
     */
    public final function orderDesc($fieldName, $tableName = null)
    {
        return $this->_prepareOrder($fieldName, $tableName, false);
    }

    /**
     * Grupowanie
     * @param string $fieldName
     * @param string $tableName
     * @return Query
     */
    public final function groupBy($fieldName, $tableName = null)
    {
        return $this->_prepareGroup($fieldName, $tableName);
    }

    /**
     * Dodaje podsekcję AND
     * @param Query $query
     * @return Query
     */
    public final function andQuery(Query $query)
    {
        return $this->_mergeQueries($query, true);
    }

    /**
     * Dodaje podsekcję WHERE (jak AND)
     * @param Query $query
     * @return Query
     */
    public final function whereQuery(Query $query)
    {
        //jest aliasem na metodę andQuery()
        return $this->andQuery($query);
    }

    /**
     * Dodaje podsekcję OR
     * @param Query $query
     * @return Query
     */
    public final function orQuery(Query $query)
    {
        return $this->_mergeQueries($query, false);
    }

    /**
     * Dodaje warunek na pole AND
     * @param string $fieldName nazwa pola
     * @param string $tableName opcjonalna nazwa tabeli źródłowej
     * @return QueryHelper\QueryField
     */
    public final function andField($fieldName, $tableName = null)
    {
        return new QueryHelper\QueryField($this, $this->_prepareField($fieldName, $tableName), 'AND');
    }

    /**
     * Pierwszy warunek w zapytaniu (domyślnie AND)
     * @param string $fieldName nazwa pola
     * @param string $tableName opcjonalna nazwa tabeli źródłowej
     * @return QueryHelper\QueryField
     */
    public final function where($fieldName, $tableName = null)
    {
        return $this->andField($fieldName, $tableName);
    }

    /**
     * Dodaje warunek na pole OR
     * @param string $fieldName nazwa pola
     * @param string $tableName opcjonalna nazwa tabeli źródłowej
     * @return QueryHelper\QueryField
     */
    public final function orField($fieldName, $tableName = null)
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
    public final function join($tableName, $targetTableName = null, $alias = null)
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
    public final function joinLeft($tableName, $targetTableName = null, $alias = null)
    {
        return new QueryHelper\QueryJoin($this, $tableName, 'LEFT JOIN', $targetTableName, $alias);
    }

    /**
     * Zwraca skompilowane zapytanie
     * @return QueryCompile
     */
    public final function getQueryCompile()
    {
        return $this->_compile;
    }

    /**
     * Zwraca skrót MD5 zapytania
     * @return string
     */
    public final function getQueryCompileHash()
    {
        return md5(str_replace(array_keys($this->_compile->bind), array_values($this->_compile->bind), print_r($this->_compile, true)));
    }

    /**
     * Zwraca nazwę klasy DAO
     * @return string
     */
    public final function getTableName()
    {
        return $this->_tableName;
    }

    /**
     * Zwraca nazwę klasy rekordu
     * @return string
     */
    public final function getRecordName()
    {
        //konwencja nazwy na rekord
        return self::_classPrefix() . 'Record';
    }

    /**
     * Zwraca nazwę klasy zapytania
     * @return string
     */
    public static final function getCollectionName()
    {
        return '\Mmi\Orm\RecordCollection';
        //konwencja nazwy na kolekcję
        //return self::_classPrefix() . 'RecordCollection';
    }

    /**
     * Resetuje sortowanie w zapytaniu
     * @return Query
     */
    public final function resetOrder()
    {
        $this->_compile->order = '';
        return $this;
    }

    /**
     * Resetuje grupowanie
     * @return Query
     */
    public final function resetGroupBy()
    {
        $this->_compile->groupBy = '';
        return $this;
    }

    /**
     * Resetuje warunki w zapytaniu
     * @return Query
     */
    public final function resetWhere()
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
    public final function count($column = '*')
    {
        return (new QueryData($this))
                ->count($column);
    }

    /**
     * Pobiera pierwszy rekord po kluczu głównym ID
     * null jeśli brak danych
     * @param int $id
     * @return Record
     */
    public final function findPk($id)
    {
        //zwróci null jeśli brak danych
        return $this->where('id')->equals($id)
                ->findFirst();
    }

    /**
     * Pobiera wszystkie rekordy i zwraca ich kolekcję
     * @return RecordCollection
     */
    public final function find()
    {
        return (new QueryData($this))->find();
    }

    /**
     * Delete complete collection
     */
    public final function delete()
    {
        //iterate records
        foreach ((new QueryData($this))->find() as $record) {
            $record->delete();
        }
    }

    /**
     * Pobiera obiekt pierwszy ze zbioru
     * null jeśli brak danych
     * @param Query $q Obiekt zapytania
     * @return RecordRo
     */
    public final function findFirst()
    {
        return (new QueryData($this))
                ->findFirst();
    }

    /**
     * Zwraca tablicę asocjacyjną (pary)
     * @param string $keyName
     * @param string $valueName
     * @return array
     */
    public final function findPairs($keyName, $valueName)
    {
        return (new QueryData($this))
                ->findPairs($keyName, $valueName);
    }

    /**
     * Pobiera wartość maksymalną z kolumny
     * @param string $keyName nazwa klucza
     * @return string wartość maksymalna
     */
    public final function findMax($keyName)
    {
        return (new QueryData($this))
                ->findMax($keyName);
    }

    /**
     * Pobiera wartość minimalną z kolumny
     * @param string $keyName nazwa klucza
     * @return string wartość minimalna
     */
    public final function findMin($keyName)
    {
        return (new QueryData($this))
                ->findMin($keyName);
    }

    /**
     * Pobiera sumę z kolumny
     * @param string $keyName nazwa klucza
     * @return string suma
     */
    public final function findSum($keyName)
    {
        return (new QueryData($this))
                ->findSum($keyName);
    }

    /**
     * Pobiera unikalne wartości kolumny
     * @param string $keyName nazwa klucza
     * @return array mixed wartości unikalne
     */
    public final function findUnique($keyName)
    {
        return (new QueryData($this))
                ->findUnique($keyName);
    }

    /**
     * Łączy query
     * @param boolean $type
     * @return Query
     */
    protected final function _mergeQueries(Query $query, $and = true)
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
    protected final function _prepareField($fieldName, $forcedTableName = null)
    {
        //funkcja sortująca rand()
        if ($fieldName == 'RAND()') {
            return $this->db->prepareField('RAND()');
        }
        //nazwa tabeli
        $tableName = ($forcedTableName === null) ? $this->_tableName : $forcedTableName;
        //przygotowany prefix tabeli
        $tablePrefix = $this->db->prepareTable($tableName);
        //pole występuje w tabeli (jeden do jednego bez żadnych konwersji)
        if ($this->dbInformation->isTableContainsField($tableName, $fieldName)) {
            return $tablePrefix . '.' . $this->db->prepareField($fieldName);
        }
        //konwersja camelcase na podkreślenia
        return $tablePrefix . '.' . $this->db->prepareField(Convert::camelcaseToUnderscore($fieldName));
    }

    /**
     * Przygotowuje order
     * @param string $fieldName
     * @param string $tableName
     * @param boolean $asc
     * @return Query
     */
    protected final function _prepareOrder($fieldName, $tableName = null, $asc = true)
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
    protected final function _prepareGroup($fieldName, $tableName = null)
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

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
class QueryData
{

    /**
     * Obiekt zapytania
     * @var \Mmi\Orm\Query
     */
    protected $_query;

    /**
     * Konstruktor
     * @param \Mmi\Orm\Query $query
     */
    public function __construct(\Mmi\Orm\Query $query)
    {
        $this->_query = $query;
    }

    /**
     * Pobiera ilość rekordów
     * @return int
     */
    public final function count($column = '*')
    {
        //wykonanie zapytania zliczającego na adapter
        $result = \Mmi\Orm\DbConnector::getAdapter()->select('COUNT(' . ($column === '*' ? '*' : \Mmi\Orm\DbConnector::getAdapter()->prepareField($column)) . ')', $this->_prepareFrom(), $this->_query->getQueryCompile()->where, $this->_query->getQueryCompile()->groupBy, '', null, null, $this->_query->getQueryCompile()->bind);
        //pobieranie rezultatu
        return isset($result[0]) ? current($result[0]) : 0;
    }

    /**
     * Pobiera wszystkie rekordy i zwraca ich kolekcję
     * @return \Mmi\Orm\RecordCollection
     */
    public final function find()
    {
        //ustalenie klasy rekordu
        $recordName = $this->_query->getRecordName();
        //ustalenie klasy kolekcji rekordów
        $collectionName = $this->_query->getCollectionName();
        //tworznie pustej kolekcji
        $collection = new $collectionName();
        //iteracja po danych z bazy
        foreach (\Mmi\Orm\DbConnector::getAdapter()->select($this->_prepareFields(), $this->_prepareFrom(), $this->_query->getQueryCompile()->where, $this->_query->getQueryCompile()->groupBy, $this->_query->getQueryCompile()->order, $this->_query->getQueryCompile()->limit, $this->_query->getQueryCompile()->offset, $this->_query->getQueryCompile()->bind) as $row) {
            //tworzenie i dodawanie rekordu
            $collection->append((new $recordName)->setFromArray($row)->clearModified());
        }
        //zwrot kolekcji
        return $collection;
    }

    /**
     * Pobiera obiekt pierwszy ze zbioru
     * null jeśli brak danych
     * @param \Mmi\Orm\Query $q Obiekt zapytania
     * @return \Mmi\Orm\RecordRo
     */
    public final function findFirst()
    {
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
    public final function findPairs($keyName, $valueName)
    {
        //inicjalizacja pustej tablicy
        $kv = [];
        //iteracja po danych
        foreach (\Mmi\Orm\DbConnector::getAdapter()->select(\Mmi\Orm\DbConnector::getAdapter()->prepareField($keyName) . ', ' . \Mmi\Orm\DbConnector::getAdapter()->prepareField($valueName), $this->_prepareFrom(), $this->_query->getQueryCompile()->where, $this->_query->getQueryCompile()->groupBy, $this->_query->getQueryCompile()->order, $this->_query->getQueryCompile()->limit, $this->_query->getQueryCompile()->offset, $this->_query->getQueryCompile()->bind) as $row) {
            //przy wybieraniu tych samych pól tabela ma tylko jeden wiersz
            $row = (count($row) == 1) ? current($row) : $row;
            //klucz to pierwszy element, wartość - drugi
            $kv[current($row)] = next($row);
        }
        return $kv;
    }

    /**
     * Pobiera wartość maksymalną z kolumny
     * @param string $keyName nazwa klucza
     * @return string wartość maksymalna
     */
    public final function findMax($keyName)
    {
        //odpytanie adaptera o rekord
        $result = \Mmi\Orm\DbConnector::getAdapter()->select('MAX(' . \Mmi\Orm\DbConnector::getAdapter()->prepareField($keyName) . ')', $this->_prepareFrom(), $this->_query->getQueryCompile()->where, $this->_query->getQueryCompile()->groupBy, $this->_query->getQueryCompile()->order, 1, null, $this->_query->getQueryCompile()->bind);
        return isset($result[0]) ? current($result[0]) : null;
    }

    /**
     * Pobiera wartość minimalną z kolumny
     * @param string $keyName nazwa klucza
     * @return string wartość minimalna
     */
    public final function findMin($keyName)
    {
        //odpytanie adaptera o rekord
        $result = \Mmi\Orm\DbConnector::getAdapter()->select('MIN(' . \Mmi\Orm\DbConnector::getAdapter()->prepareField($keyName) . ')', $this->_prepareFrom(), $this->_query->getQueryCompile()->where, $this->_query->getQueryCompile()->groupBy, $this->_query->getQueryCompile()->order, 1, null, $this->_query->getQueryCompile()->bind);
        return isset($result[0]) ? current($result[0]) : null;
    }

    /**
     * Pobiera sumę z kolumny
     * @param string $keyName nazwa klucza
     * @return string wartość minimalna
     */
    public final function findSum($keyName)
    {
        //odpytanie adaptera o rekord
        $result = \Mmi\Orm\DbConnector::getAdapter()->select('SUM(' . \Mmi\Orm\DbConnector::getAdapter()->prepareField($keyName) . ')', $this->_prepareFrom(), $this->_query->getQueryCompile()->where, $this->_query->getQueryCompile()->groupBy, $this->_query->getQueryCompile()->order, 1, null, $this->_query->getQueryCompile()->bind);
        return isset($result[0]) ? current($result[0]) : null;
    }

    /**
     * Pobiera unikalne wartości kolumny
     * @param string $keyName nazwa klucza
     * @return array mixed wartości unikalne
     */
    public final function findUnique($keyName)
    {
        //inicjalizacja pustej tabeli
        $result = [];
        //iteracja po danych
        foreach (\Mmi\Orm\DbConnector::getAdapter()->select('DISTINCT(' . \Mmi\Orm\DbConnector::getAdapter()->prepareField($keyName) . ')', $this->_prepareFrom(), $this->_query->getQueryCompile()->where, $this->_query->getQueryCompile()->groupBy, $this->_query->getQueryCompile()->order, null, null, $this->_query->getQueryCompile()->bind) as $row) {
            //dodawanie kolumny
            $result[] = current($row);
        }
        return $result;
    }

    /**
     * Przygotowuje pola do selecta
     * @return string
     */
    protected final function _prepareFields()
    {
        //jeśli pusty schemat połączeń
        if (empty($this->_query->getQueryCompile()->joinSchema)) {
            return '*';
        }
        $fields = '';
        //pobranie struktury tabeli
        $mainStructure = \Mmi\Orm\DbConnector::getTableStructure($this->_query->getTableName());
        //przygotowanie tabeli
        $table = \Mmi\Orm\DbConnector::getAdapter()->prepareTable($this->_query->getTableName());
        //iteracja po polach tabeli głównej
        foreach ($mainStructure as $fieldName => $info) {
            //dodawanie pola
            $fields .= $table . '.' . \Mmi\Orm\DbConnector::getAdapter()->prepareField($fieldName) . ', ';
        }
        //pola z tabel dołączonych
        foreach ($this->_query->getQueryCompile()->joinSchema as $schema) {
            //pobranie struktury tabeli dołączonej
            $structure = \Mmi\Orm\DbConnector::getTableStructure($schema[0]);
            //alias połączenia
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
    protected final function _prepareFrom()
    {
        //przygotowanie tabeli
        $table = \Mmi\Orm\DbConnector::getAdapter()->prepareTable($this->_query->getTableName());
        //jeśli brak joinów sama tabela
        if (empty($this->_query->getQueryCompile()->joinSchema)) {
            return $table;
        }
        //tabela do której jest łączenie
        $baseTable = $table;
        //przygotowanie joinów
        foreach ($this->_query->getQueryCompile()->joinSchema as $schema) {
            //tabela docelowa
            $targetTable = isset($schema[3]) ? $schema[3] : $baseTable;
            //typ połączenia (zwykłe, lewe)
            $joinType = isset($schema[4]) ? $schema[4] : 'JOIN';
            //alias
            $joinAlias = isset($schema[5]) ? $schema[5] : $schema[0];
            //przygotowanie sql
            $table .= ' ' . $joinType . ' ' . \Mmi\Orm\DbConnector::getAdapter()->prepareTable($schema[0]) . ' AS ' . \Mmi\Orm\DbConnector::getAdapter()->prepareTable($joinAlias) . ' ON ' .
                \Mmi\Orm\DbConnector::getAdapter()->prepareTable($joinAlias) . '.' . \Mmi\Orm\DbConnector::getAdapter()->prepareField($schema[1]) .
                ' = ' . \Mmi\Orm\DbConnector::getAdapter()->prepareTable($targetTable) . '.' . \Mmi\Orm\DbConnector::getAdapter()->prepareField($schema[2]);
        }
        return $table;
    }

}

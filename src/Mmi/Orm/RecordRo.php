<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Orm;

/**
 * Klasa rekordu tylko do odczytu
 *
 * @deprecated since 3.8 to be removed in 4.0
 */
class RecordRo
{

    /**
     * Przechowuje ekstra opcje rekordu
     * @var array
     */
    protected $_options = [];

    /**
     * Rekord wypełniony przez setFromArray
     * @var boolean
     */
    protected $_filled = false;

    /**
     * Przechowuje dołączone dane (JOIN)
     * @var array
     */
    protected $_joined = [];

    /**
     * Stan rekordu przed modyfikacją
     * @var array
     */
    protected $_state = [];

    /**
     * Nazwa klasy DAO
     * @var string
     */
    protected $_queryClass;

    /**
     * Konstruktor
     * @param mixed $id identyfikator do tworzenia obiektu
     * @throws RecordNotFoundException
     */
    public final function __construct($id = null)
    {
        $this->_queryClass = substr(get_called_class(), 0, -6) . 'Query';
        if ($id === null) {
            return;
        }
        $query = $this->_queryClass;
        if (null === ($record = (new $query)->findPk($id))) {
            throw new RecordNotFoundException('Record not found: ' . $id);
        }
        //ustawianie z tablicy i zapis stanu
        $this->setFromArray($record->toArray())
            ->clearModified();
    }

    /**
     * Pobiera klucz główny (tabela jeśli wielokrotny)
     * @return mixed klucz główny
     */
    public final function getPk()
    {
        return property_exists($this, 'id') ? $this->id : null;
    }

    /**
     * Magicznie pobiera dane z rekordu
     * @param string $name nazwa
     * @return mixed
     * @throws RecordFieldException
     */
    public final function __get($name)
    {
        throw new RecordFieldException('Field not found: ' . $name);
    }

    /**
     * Magicznie ustawia dane w rekordzie
     * @param string $name nazwa
     * @param mixed $value wartość
     * @throws RecordFieldException
     */
    public final function __set($name, $value)
    {
        throw new RecordFieldException('Field not found: ' . $name);
    }

    /**
     * Ustawia opcję w rekordzie
     * @param string $name
     * @return mixed
     */
    public final function getOption($name)
    {
        return isset($this->_options[$name]) ? $this->_options[$name] : null;
    }

    /**
     * Zwraca wszystkie opcje w rekordzie
     * @return array
     */
    public final function getOptions()
    {
        return $this->_options;
    }

    /**
     * Ustawia opcję w rekordzie
     * @param string $name
     * @param mixed $value
     * @return \Mmi\Orm\RecordRo
     */
    public final function setOption($name, $value)
    {
        $this->_options[$name] = $value;
        return $this;
    }

    /**
     * Pobiera dołączony rekord (JOIN)
     * @param string $tableName
     * @return \Mmi\Orm\Record
     */
    public final function getJoined($tableName)
    {
        return isset($this->_joined[$tableName]) ? $this->_joined[$tableName] : null;
    }

    /**
     * Sprawdza czy rekord został wypełniony setFromArray
     * @return boolean
     */
    public final function getFilled()
    {
        return $this->_filled ? true : false;
    }

    /**
     * Ustawia dane w obiekcie na podstawie tabeli
     * @param array $row tabela z danymi
     * @param bool $fromDb czy z bazy danych
     * @return \Mmi\Orm\Record
     */
    public function setFromArray(array $row = [])
    {
        $joinedRows = [];
        foreach ($row as $key => $value) {
            //przyjęcie pól z joinów
            if (false !== strpos($key, '__')) {
                $keyParts = explode('__', $key);
                $joinedRows[$keyParts[0]][$keyParts[1]][$keyParts[2]] = $value;
                continue;
            }
            $field = \Mmi\Orm\Convert::underscoreToCamelcase($key);
            if (property_exists($this, $field)) {
                $this->$field = $value;
                continue;
            }
            $this->setOption($field, $value);
        }
        //podpięcie joinów pod główny rekord
        foreach ($joinedRows as $alias => $tables) {
            foreach ($tables as $tableName => $fields) {
                $recordClass = \Mmi\Orm\DbConnector::getRecordNameByTable($tableName);
                $record = new $recordClass;
                $record->setFromArray($fields)
                    ->clearModified();
                $this->_joined[$alias] = $record;
            }
        }
        //wypełniony
        $this->_filled = true;
        return $this;
    }

    /**
     * Usuwa flagę modyfikacji na polu, lub wszyskich polach
     * @return \Mmi\Orm\RecordRo
     */
    public final function clearModified()
    {
        foreach ($this as $name => $value) {
            $this->_state[$name] = $value;
        }
        return $this;
    }

    /**
     * Zwraca czy zmodyfikowano pole
     * @param string $field nazwa pola
     * @return boolean
     */
    public final function isModified($field)
    {
        //brak klucza w tablicy stanu - niezmodyfikowane
        if (!array_key_exists($field, $this->_state)) {
            return false;
        }
        //wartość liczbowa - porównanie proste
        if (is_numeric($this->$field) && $this->_state[$field] == $this->$field) {
            return false;
        }
        //porównanie z typem
        return ($this->_state[$field] !== $this->$field);
    }

    /**
     * Zwraca wartość startową pola
     * @param string $field nazwa pola
     * @return mixed
     */
    public final function getInitialStateValue($field)
    {
        return isset($this->_state[$field]) ? $this->_state[$field] : null;
    }

    /**
     * Zwraca dane z obiektu w postaci tablicy
     * @return array
     */
    public function toArray()
    {
        //tworzy array z opcji
        $array = $this->_options;
        //dołącza joinowane tabele
        foreach ($this->_joined as $name => $value) {
            if ($value instanceof \Mmi\Orm\RecordRo) {
                $value = $value->toArray();
            }
            $array[$name] = $value;
        }
        //dołącza pola obiektu
        foreach ($this as $name => $value) {
            //tylko publiczne zmienne
            if (substr($name, 0, 1) == '_') {
                continue;
            }
            $array[$name] = $value;
        }
        return $array;
    }

    /**
     * Zwraca dane z obiektu w postaci JSON
     * @return array
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * WHERE po kluczu tabeli
     * @param string $bindKey nazwa do binda
     * @return string
     */
    protected function _pkWhere($bindKey)
    {
        return 'WHERE ' . \Mmi\Orm\DbConnector::getAdapter()->prepareField('id') . ' = :' . $bindKey;
    }

}

<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Orm;

use Mmi\Db\Adapter\PdoBindHelper;

/**
 * Klasa rekordu ORM
 *
 * @deprecated since 3.8 to be removed in 4.0
 */
class Record extends \Mmi\Orm\RecordRo
{

    /**
     * Zapis danych do obiektu
     * @return bool
     */
    public function save()
    {
        if ($this->getPk() !== null && !empty($this->_state)) {
            return $this->_update();
        }
        return $this->_insert();
    }

    /**
     * Kasowanie obiektu
     * @return boolean
     */
    public function delete()
    {
        if ($this->getPk() === null) {
            return false;
        }
        $query = $this->_queryClass;
        $bindKey = PdoBindHelper::generateBindKey();
        $result = DbConnector::getAdapter()->delete((new $query)->getTableName(), $this->_pkWhere($bindKey), [$bindKey => $this->getInitialStateValue('id')]);
        return ($result > 0) ? true : false;
    }

    /**
     * Wstawienie danych (przez save)
     * @return bool
     */
    protected function _insert()
    {
        $query = $this->_queryClass;
        $table = (new $query)->getTableName();
        $result = DbConnector::getAdapter()->insert($table, $this->_truncateToStructure());
        //odczyt id z sekwencji
        if ($result && property_exists($this, 'id') && $this->getPk() === null) {
            $this->id = DbConnector::getAdapter()->lastInsertId(DbConnector::getAdapter()->prepareSequenceName($table));
        }
        //utrwalanie bieżącego stanu
        $this->clearModified();
        return true;
    }

    /**
     * Aktualizacja danych (przez save)
     * @return bool
     */
    protected function _update()
    {
        $query = $this->_queryClass;
        $bindKey = PdoBindHelper::generateBindKey();
        $result = DbConnector::getAdapter()->update((new $query)->getTableName(), $this->_truncateToStructure(true), $this->_pkWhere($bindKey), [$bindKey => $this->getInitialStateValue('id')]);
        //utrwalanie bieżącego stanu
        $this->clearModified();
        return ($result >= 0);
    }

    /**
     * Obcina nadmiarowe dane w obiekcie zgodnie ze strukturą bazy danych
     * @param bool $modifiedOnly tylko zmodyfikowane
     * @return array
     */
    protected final function _truncateToStructure($modifiedOnly = false)
    {
        $tableData = [];
        $query = $this->_queryClass;
        $structure = DbConnector::getTableStructure((new $query)->getTableName());
        foreach ($this as $field => $value) {
            //jeśli tylko zmodyfikowane i pole nie jest modyfikowane - omijanie
            if ($modifiedOnly && !$this->isModified($field)) {
                continue;
            }
            //konwersja na nazwy bazy
            if (!isset($structure[$field])) {
                $field = Convert::camelcaseToUnderscore($field);
                if (!isset($structure[$field])) {
                    continue;
                }
            }
            //próba zapisania nullowej wartości do nie nullowej kolumny (ID)
            if ($value === null && !$structure[$field]['null']) {
                continue;
            }
            $tableData[$field] = $value;
        }
        return $tableData;
    }

}

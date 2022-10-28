<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Model;

/**
 * Kolekcja obiektów transferu
 */
class DtoCollection extends \ArrayObject
{
    /**
     * Konstruktor ustawiający kolekcję na podstawie tablicy obiektów lub tablic
     * @param mixed $data
     */
    public function __construct($data = null)
    {
        if ($data === null) {
            return;
        }
        if ($data instanceof \Mmi\Orm\RecordCollection) {
            $this->setFromDaoRecordCollection($data);
            return;
        }
        if (!is_array($data) || empty($data)) {
            return;
        }
        if (!is_array(reset($data))) {
            parent::__construct($data);
            return;
        }
        $this->setFromArray($data);
    }

    /**
     * Ustawia kolekcję na podstawie tablicy tablic
     * @param array $data tablica obiektów \stdClass
     * @return \Mmi\Model\Api\DtoCollection
     */
    final public function setFromArray(array $data)
    {
        $dtoClass = $this->_getDtoClass();
        $this->exchangeArray([]);
        foreach ($data as $array) {
            if (!is_array($array)) {
                continue;
            }
            $this->append(new $dtoClass($array));
        }
        return $this;
    }

    /**
     * Ustawia kolekcję na podstawie obiektu obiektów
     * @param \Mmi\Orm\RecordCollection $data kolekcja obiektów DAO
     * @return \Mmi\Model\Api\Orm\DtoCollection
     */
    final public function setFromDaoRecordCollection(\Mmi\Orm\RecordCollection $data)
    {
        $dtoClass = $this->_getDtoClass();
        $this->exchangeArray([]);
        foreach ($data as $record) {
            $this->append(new $dtoClass($record));
        }
        return $this;
    }

    /**
     * Zwraca kolekcję w postaci tablicy
     * @return array
     */
    final public function toArray()
    {
        $array = [];
        foreach ($this as $key => $dto) {
            $array[$key] = $dto->toArray();
        }
        return $array;
    }

    /**
     * Zwraca kolekcję w postaci tablicy obiektów DTO
     * @return array
     */
    final public function toObjectArray()
    {
        $array = [];
        foreach ($this as $key => $dto) {
            $array[$key] = $dto;
        }
        return $array;
    }

    /**
     * Ustala nazwę klasy DTO
     * @return string
     */
    final protected function _getDtoClass()
    {
        $dtoClass = substr(get_class($this), 0, -10);
        if ($dtoClass == '\Mmi\Model\Dto') {
            throw new DtoException('Invalid DTO object name');
        }
        return $dtoClass;
    }
}

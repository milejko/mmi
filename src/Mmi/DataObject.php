<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi;

class DataObject implements \Iterator
{

    /**
     * Dane
     * @var array
     */
    protected $_data = [];

    /**
     * Konstruktor ustawia obiekt
     * @param array $data zmienne requestu
     * @param boolean $shallow
     */
    public function __construct(array $data = [])
    {
        $this->setParams($data, false);
    }

    /**
     * Magicznie pobiera zmienną
     * @param string $key klucz
     * @return mixed
     */
    public function __get($key)
    {
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }

    /**
     * Magicznie ustawia zmienną
     * @param string $key klucz
     * @param mixed $value wartość
     */
    public function __set($key, $value)
    {
        $this->_data[$key] = $value;
    }

    /**
     * Magicznie sprawdza istnienie zmiennej
     * @param string $key klucz
     * @return boolean
     */
    public function __isset($key)
    {
        return isset($this->_data[$key]);
    }

    /**
     * Magicznie usuwa zmienną
     * @param string $key klucz
     */
    public function __unset($key)
    {
        unset($this->_data[$key]);
    }

    /**
     * Ustawia wszystkie zmienne
     * @param array $data parametry
     * @param bool $reset usuwa wcześniej istniejące parametry
     * @return \Mmi\DataObject
     */
    public function setParams(array $data = [], $reset = false)
    {
        if ($reset) {
            $this->_data = $data;
        }
        foreach ($data as $key => $value) {
            $this->__set($key, $value);
        }
        return $this;
    }

    /**
     * Zwraca informację o pustości obiektu
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->_data);
    }

    /**
     * Zwraca wszystkie dane w formie tabeli
     * @return array
     */
    public function toArray()
    {
        return $this->_data;
    }

    /**
     * Przeskakuje na początek
     * @return mixed
     */
    function rewind()
    {
        return reset($this->_data);
    }

    /**
     * Zwraca aktualny element
     * @return mixed
     */
    function current()
    {
        return current($this->_data);
    }

    /**
     * Zwraca klucz aktualnego elementu
     * @return string
     */
    function key()
    {
        return key($this->_data);
    }

    /**
     * Przechodzi do następnego elementu
     * @return mixed
     */
    function next()
    {
        return next($this->_data);
    }

    /**
     * Sprawdza, czy aktualna pozycja jest prawidłowa
     * @return boolean
     */
    function valid()
    {
        return key($this->_data) !== null;
    }
}

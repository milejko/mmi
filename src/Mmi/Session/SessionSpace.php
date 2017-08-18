<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Session;

class SessionSpace
{

    /**
     * Nazwa przestrzeni
     * @var string
     */
    private $_namespace;

    /**
     * Konstruktor, ustawia nazwę przestrzeni
     * @param string $namespace nazwa przestrzeni
     */
    public function __construct($namespace)
    {
        $this->_namespace = $namespace;
    }

    /**
     * Statyczna fabryka
     * @param string $namespace
     * @return \Mmi\Session\SessionSpace
     */
    public static function factory($namespace)
    {
        return new self($namespace);
    }

    /**
     * Magicznie ustawia zmienną w przestrzeni
     * @param string $key klucz
     * @param string $value wartość
     */
    public function __set($key, $value)
    {
        if (!isset($_SESSION[$this->_namespace]) || !is_array($_SESSION[$this->_namespace])) {
            $_SESSION[$this->_namespace] = [];
        }
        $_SESSION[$this->_namespace][$key] = $value;
    }

    /**
     * Magicznie pobiera zmienną z przestrzeni
     * @param string $key klucz
     * @return mixed
     */
    public function __get($key)
    {
        return isset($_SESSION[$this->_namespace][$key]) ? $_SESSION[$this->_namespace][$key] : null;
    }

    /**
     * Magicznie sprawdza istnienie zmiennej
     * @param string $key klucz
     * @return boolean
     */
    public function __isset($key)
    {
        return isset($_SESSION[$this->_namespace][$key]);
    }

    /**
     * Magicznie usuwa zmienną z przestrzeni
     * @param string $key klucz
     */
    public function __unset($key)
    {
        unset($_SESSION[$this->_namespace][$key]);
    }

    /**
     * Usuwa wszystkie zmienne
     */
    public function unsetAll()
    {
        unset($_SESSION[$this->_namespace]);
    }

    /**
     * Ustawia namespace z tabeli
     * @param array $data
     * @return \Mmi\Session\SessionSpace
     */
    public function setFromArray(array $data)
    {
        $_SESSION[$this->_namespace] = $data;
        return $this;
    }

    /**
     * Zrzuca namespace do tabeli
     * @return string
     */
    public function toArray()
    {
        return (isset($_SESSION[$this->_namespace]) && is_array($_SESSION[$this->_namespace])) ? $_SESSION[$this->_namespace] : [];
    }

}

<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi;

/**
 * Klasa obiektu opcji
 */
class OptionObject
{

    /**
     * Opcje pola
     * @var array
     */
    protected $_options = [];

    /**
     * Konstruktor ustawia obiekt
     * @param array $data tablica zmiennych
     */
    public function __construct(array $data = [])
    {
        $this->setOptions($data);
    }

    /**
     * Ustawia opcję
     * @param string $key klucz
     * @param mixed $value wartość
     * @return self
     */
    public function setOption($key, $value)
    {
        $this->_options[$key] = $value;
        return $this;
    }

    /**
     * Zwraca opcję po kluczu
     * @param string $key klucz
     * @return mixed
     */
    public function getOption($key)
    {
        return isset($this->_options[$key]) ? $this->_options[$key] : null;
    }

    /**
     * Usuwa opcję
     * @param string $key klucz
     * @return self
     */
    public function unsetOption($key)
    {
        unset($this->_options[$key]);
        return $this;
    }

    /**
     * Sprawdza istnienie opcji
     * @param string $key klucz
     * @return boolean
     */
    public function issetOption($key)
    {
        return array_key_exists($key, $this->_options);
    }

    /**
     * Ustawia wszystkie opcje na podstawie tabeli
     * @param array $options tabela opcji
     * @param boolean $reset usuwa poprzednie wartości (domyślnie nie)
     * @return self
     */
    public function setOptions(array $options = [], $reset = false)
    {
        //jeśli reset
        if ($reset) {
            $this->_options = [];
        }
        //dopełnianie tabeli opcji
        foreach ($options as $key => $value) {
            $this->setOption($key, $value);
        }
        return $this;
    }

    /**
     * Zwraca wszystkie opcje
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Obsługa getterów i setterów np. getAddress czy setPort
     * @param string $name
     * @param array $params
     * @return mixed
     * @throws App\KernelException
     */
    public function __call($name, $params)
    {
        $matches = [];
        //obsługa getterów
        if (preg_match('/^get([a-zA-Z0-9]+)/', $name, $matches)) {
            return $this->getOption(lcfirst($matches[1]));
        }
        //obsługa setterów
        if (preg_match('/^set([a-zA-Z0-9]+)/', $name, $matches)) {
            return $this->setOption(lcfirst($matches[1]), isset($params[0]) ? $params[0] : null);
        }
        //obsługa unsetów
        if (preg_match('/^unset([a-zA-Z0-9]+)/', $name, $matches)) {
            return $this->unsetOption(lcfirst($matches[1]));
        }
        //obsługa issetów
        if (preg_match('/^isset([a-zA-Z0-9]+)/', $name, $matches)) {
            return $this->issetOption(lcfirst($matches[1]));
        }
        throw new App\KernelException('Method not found: ' . $name);
    }

}

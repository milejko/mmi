<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Mvc;

/**
 * Obiekt konfiguracji routera
 */
class RouterConfig
{

    /**
     * Dane rout
     * @var type
     */
    protected $_data = [];

    /**
     * Tworzy (lub nadpisuje) routę
     * @param string $name nazwa lub indeks
     * @param string $pattern wyrażenie regularne lub plain
     * @param array $replace tablica zastąpień
     * @param array $default tablica wartości domyślnych
     * @return \Mmi\Mvc\RouterConfig
     */
    public function setRoute($name, $pattern, array $replace = [], array $default = [])
    {
        $route = new \Mmi\Mvc\RouterConfigRoute;
        $route->name = $name;
        $route->pattern = $pattern;
        $route->replace = $replace;
        $route->default = $default;
        return $this->addRoute($route);
    }

    /**
     * Dodaje routę do stosu rout
     * @param \Mmi\Mvc\RouterConfigRoute $route
     * @return \Mmi\Mvc\RouterConfig
     */
    public function addRoute(\Mmi\Mvc\RouterConfigRoute $route)
    {
        $this->_data[$route->name] = $route;
        return $this;
    }

    /**
     * Ustawia routy
     * @param array $routes tablica z obiektami rout
     * @param boolean $replace czy zastąpić obecną tablicę
     * @return \Mmi\Mvc\RouterConfig
     */
    public function setRoutes(array $routes, $replace = false)
    {
        if ($replace) {
            $this->_data = [];
        }
        //dodaje routy z tablicy
        foreach ($routes as $route) {
            /* @var $route \Mmi\Mvc\RouterConfigRoute */
            $this->addRoute($route);
        }
        return $this;
    }

    /**
     * Zwraca wszystkie skonfigurowane routy
     * @return RouterConfigRoute[]
     */
    public function getRoutes()
    {
        return $this->_data;
    }

}

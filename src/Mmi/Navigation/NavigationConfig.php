<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Navigation;

/**
 * Klasa konfiguracji nawigatora
 */
class NavigationConfig
{

    /**
     * Dane nawigacji
     * @var array
     */
    protected $_data = [];

    /**
     * Indeks elementów
     * @var int
     */
    protected static $_index = 1000000;

    /**
     * Zbudowany obiekt
     * @var array
     */
    public $build = [];

    /**
     * Dodaje element nawigatora
     * @param \Mmi\Navigation\NavigationConfigElement $element
     * @return \Mmi\Navigation\NavigationConfig
     */
    public function addElement(\Mmi\Navigation\NavigationConfigElement $element)
    {
        $this->_data[$element->getId()] = $element;
        return $this;
    }

    /**
     * Zwraca i inkrementuje indeks elementów
     * @return int
     */
    public static function getAutoIndex()
    {
        return self::$_index++;
    }

    /**
     * Znajduje element po identyfikatorze
     * @param int $id identyfikator
     * @return \Mmi\Navigation\NavigationConfigElement
     */
    public function findById($id, $withParents = false)
    {
        $parents = [];
        foreach ($this->build() as $element) {
            if (null !== ($found = $this->_findInChildren($element, $id, $withParents, $parents))) {
                if ($withParents) {
                    $found['parents'] = array_reverse($parents);
                }
                return $found;
            }
        }
    }

    /**
     * Rekurencyjnie przeszukuje elementy potomne
     * @param $element
     * @param int $id identyfikator
     * @return array
     */
    protected function _findInChildren(array $element, $id, $withParents, array &$parents)
    {
        if ($element['id'] == $id) {
            return $element;
        }
        foreach ($element['children'] as $child) {
            if ($child['id'] == $id) {
                if ($withParents) {
                    $parents[] = $element;
                }
                return $child;
            }
            if (null !== ($found = $this->_findInChildren($child, $id, $withParents, $parents))) {
                if ($withParents) {
                    $parents[] = $element;
                }
                return $found;
            }
        }
    }

    /**
     * Buduje wszystkie obiekty
     * @return array
     */
    public function build()
    {
        if (!empty($this->build)) {
            return $this->build;
        }
        //root
        $this->build = [[
            'active' => true,
            'name' => '',
            'label' => '',
            'id' => 'root',
            'level' => 0,
            'uri' => '',
            'children' => []
        ]];
        foreach ($this->_data as $element) {
            $this->build[0]['children'][$element->getId()] = $element->build();
        }
        //usuwanie konfiguracji po zbudowaniu
        $this->_data = [];
        return $this->build;
    }

}

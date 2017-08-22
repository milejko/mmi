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
 * Klasa elementu konfiguracyjnego nawigatora
 * @method integer getId() pobiera id
 * @method array getChildren() pobiera dzieci
 * @method \Mmi\Navigation\NavigationConfigElement setId(integer $id) ustawia id
 * @method \Mmi\Navigation\NavigationConfigElement setLang(string $lang) ustawia lang
 * @method \Mmi\Navigation\NavigationConfigElement setLabel(string $label) ustawia label
 * @method \Mmi\Navigation\NavigationConfigElement setModule(string $module) ustawia moduł
 * @method \Mmi\Navigation\NavigationConfigElement setController(string $controller) ustawia kontroler
 * @method \Mmi\Navigation\NavigationConfigElement setAction(string $action) ustawia akcję
 * @method \Mmi\Navigation\NavigationConfigElement setTitle(string $title) ustawia tytuł
 * @method \Mmi\Navigation\NavigationConfigElement setDescription(string $description) ustawia opis
 * @method \Mmi\Navigation\NavigationConfigElement setUri(string $uri) ustawia uri
 * @method \Mmi\Navigation\NavigationConfigElement setDateStart(string $dateStart) ustawia datę startową
 * @method \Mmi\Navigation\NavigationConfigElement setDateEnd(string $dateEnd) ustawia datę końcową
 * 
 */
class NavigationConfigElement extends \Mmi\OptionObject
{

    /**
     * Struktura drzewiasta
     * @var array
     */
    protected $_build = [];

    /**
     * Konstruktor
     * @param string $id
     */
    public function __construct($id = null)
    {
        //ustawienie danych (parent)
        parent::__construct([
            'id' => ($id === null) ? \Mmi\Navigation\NavigationConfig::getAutoIndex() : $id,
            //wyłączony
            'disabled' => false,
            'module' => null,
            'controller' => 'index',
            'action' => 'index',
            'params' => [],
            //czy follow
            'follow' => true,
            //czy blank
            'blank' => false,
            //daty
            'dateStart' => null,
            'dateEnd' => null,
            'uri' => null,
            //tabela z elementami potomnymi
            'children' => [],
            //konfiguracja
            'config' => new \Mmi\DataObject
        ]);
    }

    /**
     * Wyłącza element
     * @param boolean $disabled
     * @return \Mmi\Navigation\NavigationConfigElement
     */
    public function setDisabled($disabled = true)
    {
        return $this->setOption('disabled', (bool) $disabled);
    }

    /**
     * Ustawia parametry
     * @param array $params
     * @return \Mmi\Navigation\NavigationConfigElement
     */
    public function setParams(array $params)
    {
        return $this->setOption('params', $params);
    }

    /**
     * Ustawia HTTPS
     * @param boolean $https
     * @return \Mmi\Navigation\NavigationConfigElement
     */
    public function setHttps($https = null)
    {
        //jeśli https null (bez zmiany)
        if ($https === null) {
            return $this->setOption('https', null);
        }
        //w pozostałych sytuacjach wymuszamy bool
        return $this->setOption('https', (bool) $https);
    }

    /**
     * Ustawia typ linku na follow
     * @param boolean $follow
     * @return \Mmi\Navigation\NavigationConfigElement
     */
    public function setFollow($follow = true)
    {
        return $this->setOption('follow', (bool) $follow);
    }

    /**
     * Ustawia target linku na blank
     * @param boolean $blank
     * @return \Mmi\Navigation\NavigationConfigElement
     */
    public function setBlank($blank = true)
    {
        return $this->setOption('blank', (bool) $blank);
    }

    /**
     * Ustawia obiekt konfiguracyjny
     * @param \Mmi\DataObject $config
     * @return \Mmi\Navigation\NavigationConfigElement
     */
    public function setConfig(\Mmi\DataObject $config)
    {
        return $this->setOption('config', $config);
    }

    /**
     * Dodaje element potomny
     * @param \Mmi\Navigation\NavigationConfigElement $element
     * @return \Mmi\Navigation\NavigationConfigElement
     */
    public function addChild(\Mmi\Navigation\NavigationConfigElement $element)
    {
        $this->_options['children'][$element->getId()] = $element;
        return $this;
    }

    /**
     * Budowanie struktury drzewiastej na podstawie konfiguracji
     * @return array
     */
    public function build()
    {
        //korzysta z klasy buildera
        return ($this->_build = NavigationConfigBuilder::build($this->_options));
    }

}

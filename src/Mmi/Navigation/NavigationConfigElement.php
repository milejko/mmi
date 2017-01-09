<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Navigation;

class NavigationConfigElement {

	/**
	 * Dane elementu
	 * @var array
	 */
	protected $_data = [
		//id
		'id' => null,
		//język
		'lang' => null,
		//wyłączony
		'disabled' => false,
		//labelka
		'label' => null,
		//moduł + kontroler + akcja + parametry
		'module' => null,
		'controller' => 'index',
		'action' => 'index',
		'params' => [],
		//tytuł
		'title' => null,
		//opis
		'description' => null,
		//uri
		'uri' => null,
		//czy https
		'https' => null,
		//czy follow
		'follow' => true,
		//czy blank
		'blank' => false,
		'config' => null,
		//data rozpoczęcia publikacji
		'dateStart' => null,
		//data wyłączenia publikacji
		'dateEnd' => null,
		//tabela z elementami potomnymi
		'children' => [],
	];

	/**
	 * Struktura drzewiasta
	 * @var array
	 */
	protected $_build = [];

	/**
	 * Konstruktor
	 * @param string $id
	 */
	public function __construct($id = null) {
		$this->_data['config'] = new \Mmi\DataObject();
		$this->_data['id'] = ($id === null) ? \Mmi\Navigation\NavigationConfig::getAutoIndex() : $id;
	}
	
	/**
	 * Pobiera wartość
	 * @param string $name
	 * @return mixed
	 */
	public function get($name) {
		return isset($this->_data[$name]) ? $this->_data[$name] : null;
	}

	/**
	 * Ustawia wartość
	 * @param string $name
	 * @param string $value
	 * @return \Mmi\Navigation\NavigationConfigElement
	 */
	public function set($name, $value) {
		$this->_data[$name] = $value;
		return $this;
	}

	/**
	 * Pobieranie ID
	 * @return integer
	 */
	public function getId() {
		return $this->get('id');
	}
	
	/**
	 * Ustawia ID
	 * @param integer $id
	 * @return \Mmi\Navigation\NavigationConfigElement
	 */
	public function setId($id) {
		return $this->set('id', $id);
	}

	/**
	 * Pobiera dzieci
	 * @return array
	 */
	public function getChildren() {
		return $this->get('children');
	}
	
	/**
	 * Ustawia język
	 * @param string $lang
	 * @return \Mmi\Navigation\NavigationConfigElement
	 */
	public function setLang($lang) {
		return $this->set('lang', $lang);
	}

	/**
	 * Wyłącza element
	 * @param boolean $disabled
	 * @return \Mmi\Navigation\NavigationConfigElement
	 */
	public function setDisabled($disabled = true) {
		return $this->set('disabled', (bool) $disabled);
	}

	/**
	 * Ustawia labelkę
	 * @param string $label
	 * @return \Mmi\Navigation\NavigationConfigElement
	 */
	public function setLabel($label) {
		return $this->set('label', $label);
	}

	/**
	 * Ustawia moduł
	 * @param string $module
	 * @return \Mmi\Navigation\NavigationConfigElement
	 */
	public function setModule($module) {
		return $this->set('module', $module);
	}

	/**
	 * Ustawia kontroler
	 * @param string $controller
	 * @return \Mmi\Navigation\NavigationConfigElement
	 */
	public function setController($controller) {
		return $this->set('controller', $controller);
	}

	/**
	 * Ustawia akcję
	 * @param string $action
	 * @return \Mmi\Navigation\NavigationConfigElement
	 */
	public function setAction($action) {
		return $this->set('action', $action);
	}

	/**
	 * Ustawia parametry
	 * @param array $params
	 * @return \Mmi\Navigation\NavigationConfigElement
	 */
	public function setParams(array $params) {
		return $this->set('params', $params);
	}

	/**
	 * Ustawia tytuł
	 * @param string $title
	 * @return \Mmi\Navigation\NavigationConfigElement
	 */
	public function setTitle($title) {
		return $this->set('title', $title);
	}

	/**
	 * Ustawia opis
	 * @param string $description
	 * @return \Mmi\Navigation\NavigationConfigElement
	 */
	public function setDescription($description) {
		return $this->set('description', $description);
	}

	/**
	 * Ustawia uri
	 * @param string $uri
	 * @return \Mmi\Navigation\NavigationConfigElement
	 */
	public function setUri($uri) {
		return $this->set('uri', $uri);
	}

	/**
	 * Ustawia HTTPS
	 * @param boolean $https
	 * @return \Mmi\Navigation\NavigationConfigElement
	 */
	public function setHttps($https = null) {
		//jeśli https null (bez zmiany)
		if ($https === null) {
			return $this->set('https', null);
		}
		//w pozostałych sytuacjach wymuszamy bool
		return $this->set('https', (bool) $https);
	}

	/**
	 * Ustawia typ linku na follow
	 * @param boolean $follow
	 * @return \Mmi\Navigation\NavigationConfigElement
	 */
	public function setFollow($follow = true) {
		return $this->set('follow', (bool) $follow);
	}

	/**
	 * Ustawia target linku na blank
	 * @param boolean $blank
	 * @return \Mmi\Navigation\NavigationConfigElement
	 */
	public function setBlank($blank = true) {
		return $this->set('blank', (bool) $blank);
	}
	
	/**
	 * Ustawia obiekt konfiguracyjny
	 * @param \Mmi\DataObject $config
	 * @return \Mmi\Navigation\NavigationConfigElement
	 */
	public function setConfig(\Mmi\DataObject $config) {
		return $this->set('config', $config);
	}

	/**
	 * Ustawia datę włączenia węzła
	 * @param string $dateStart
	 * @return \Mmi\Navigation\NavigationConfigElement
	 */
	public function setDateStart($dateStart) {
		return $this->set('dateStart', $dateStart);
	}

	/**
	 * Ustawia datę wyłączenia węzła
	 * @param string $dateEnd
	 * @return \Mmi\Navigation\NavigationConfigElement
	 */
	public function setDateEnd($dateEnd) {
		return $this->set('dateEnd', $dateEnd);
	}

	/**
	 * Dodaje element potomny
	 * @param \Mmi\Navigation\NavigationConfigElement $element
	 * @return \Mmi\Navigation\NavigationConfigElement
	 */
	public function addChild(\Mmi\Navigation\NavigationConfigElement $element) {
		$this->_data['children'][$element->getId()] = $element;
		return $this;
	}

	/**
	 * Budowanie struktury drzewiastej na podstawie konfiguracji
	 * @return array
	 */
	public function build() {
		//korzysta z klasy buildera
		return ($this->_build = NavigationConfigBuilder::build($this->_data));
	}

}

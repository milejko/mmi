<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi;

/**
 * Klasa widoku
 * @method string url(array $params = [], $reset = false, $absolute = false, $https = null, array $unset = [])
 * @method string widget($module, $controller = 'index', $action = 'index', array $params = [])
 * @method View\Helper\Navigation navigation()
 */
class View extends \Mmi\DataObject {

	/**
	 * Bieżąca wersja językowa
	 * @var string
	 */
	private $_locale;

	/**
	 * Tabela z załadowanymi helperami
	 * @var array
	 */
	private $_helpers = [];

	/**
	 * Tabela z załadowanymi filtrami
	 * @var array
	 */
	private $_filters = [];

	/**
	 * Przechowuje dane placeholderów
	 * @var array
	 */
	private $_placeholders = [];

	/**
	 * Wyłączony
	 * @var boolean
	 */
	private $_layoutDisabled = false;

	/**
	 * Obiekt tłumaczeń
	 * @var \Mmi\Translate
	 */
	private $_translate;

	/**
	 * Obiekt buforujący
	 * @var \Mmi\Cache
	 */
	private $_cache;

	/**
	 * Włączone buforowanie
	 * @var boolean
	 */
	private $_alwaysCompile = true;

	/**
	 * Obiekt requestu
	 * @var \Mmi\Controller\Request
	 */
	public $request;

	/**
	 * Bazowa ścieżka
	 * @var string
	 */
	public $baseUrl;

	/**
	 * Magicznie wywołuje metodę na widoku
	 * przekierowuje wywołanie na odpowiedni helper
	 * @param string $name nazwa metody
	 * @param array $params parametry
	 * @return mixed
	 */
	public function __call($name, array $params = []) {
		$helper = $this->getHelper($name);
		//poprawny helper
		if ($helper instanceof \Mmi\View\Helper\HelperAbstract) {
			return call_user_func_array([$helper, $name], $params);
		}
		return $this->getPlaceholder($name);
	}

	/**
	 * Ustawia obiekt request
	 * @param \Mmi\Controller\Request $request
	 * @return \Mmi\View
	 */
	public function setRequest(\Mmi\Controller\Request $request) {
		$this->request = $request;
		$this->module = $request->getModuleName();
		$this->lang = $request->lang;
		return $this;
	}

	/**
	 * Ustawia translator
	 * @param \Mmi\Translate $translate
	 * @return \Mmi\View
	 */
	public function setTranslate(\Mmi\Translate $translate) {
		$this->_translate = $translate;
		return $this;
	}

	/**
	 * Ustawia obiekt cache
	 * @param \Mmi\Cache $cache
	 * @return \Mmi\View
	 */
	public function setCache(\Mmi\Cache $cache) {
		$this->_cache = $cache;
		return $this;
	}

	/**
	 * Ustawia opcję zawsze kompiluj szablony
	 * @param boolean $compile
	 * @return \Mmi\View
	 */
	public function setAlwaysCompile($compile = true) {
		$this->_alwaysCompile = $compile;
		return $this;
	}

	/**
	 * Ustawia bazowy url
	 * @param string $baseUrl
	 * @return \Mmi\View
	 */
	public function setBaseUrl($baseUrl) {
		$this->baseUrl = $baseUrl;
		return $this;
	}

	/**
	 * Zwraca obiekt translatora
	 * @return \Mmi\Translate
	 */
	public function getTranslate() {
		return ($this->_translate !== null) ? $this->_translate : new \Mmi\Translate();
	}

	/**
	 * Zwraca obiekt cache
	 * @return \Mmi\Cache
	 */
	public function getCache() {
		return $this->_cache;
	}

	/**
	 * Pobiera helper na podstawie nazwy z uwzględnieniem ścieżek do helperów
	 * @param string $name nazwa
	 * @return \Mmi\View\Helper\HelperAbstract
	 */
	public function getHelper($name) {
		$structure = \Mmi\Controller\Front::getInstance()->getStructure('helper');
		//położenie helpera w strukturze
		foreach ($structure as $namespace => $helpers) {
			if (!isset($helpers[$name])) {
				continue;
			}
			$className = '\\' . $namespace . '\\View\\Helper\\' . ucfirst($name);
		}
		//brak helpera
		if (!isset($className)) {
			return false;
		}
		//helper już zarejestrowany
		if (isset($this->_helpers[$className])) {
			return $this->_helpers[$className];
		}
		//zwrot nowej klasy
		return $this->_helpers[$className] = new $className();
	}

	/**
	 * Pobiera filtr na podstawie nazwy z uwzględnieniem ścieżek do filtrów
	 * @param string $name nazwa
	 * @return \Mmi\View\Helper\HelperAbstract
	 */
	public function getFilter($name) {
		$structure = \Mmi\Controller\Front::getInstance()->getStructure('filter');
		foreach ($structure as $namespace => $filters) {
			if (!isset($filters[$name])) {
				continue;
			}
			$className = '\\' . $namespace . '\\Filter\\' . ucfirst($name);
		}
		if (!isset($className)) {
			throw new \Exception('Filter not found: ' . $name);
		}
		if (isset($this->_filters[$className])) {
			return $this->_filters[$className];
		}
		return $this->_filters[$className] = new $className();
	}

	/**
	 * Ustawia placeholder
	 * @param string $name nazwa
	 * @param string $content zawartość
	 * @return \Mmi\View
	 */
	public function setPlaceholder($name, $content) {
		$this->_placeholders[$name] = $content;
		return $this;
	}

	/**
	 * Pobiera placeholder
	 * @param string $name nazwa
	 * @return string
	 */
	public function getPlaceholder($name) {
		return isset($this->_placeholders[$name]) ? $this->_placeholders[$name] : null;
	}

	/**
	 * Pobiera wszystkie zmienne w postaci tablicy
	 * @return array
	 */
	public function getAllVariables() {
		return $this->_data;
	}

	/**
	 * Renderuje i zwraca wynik wykonania template
	 * @param string $module moduł
	 * @param string $controller kontroler
	 * @param string $action akcja
	 * @param bool $fetch przekaż wynik wywołania w zmiennej
	 */
	public function renderTemplate($module, $controller, $action) {
		return $this->render($this->_getTemplate($module, $controller, $action));
	}

	/**
	 * Generowanie kodu PHP z kodu szablonu w locie
	 * @param string $input kod szablonu
	 * @return string kod PHP
	 */
	public function renderDirectly($input) {
		//przechwytywanie zawartości bufora
		$hash = md5($input);
		$inputBuffer = ob_get_contents();
		ob_clean();
		//ustawianie języka z translate'a
		if (!$this->_locale && $this->_translate !== null) {
			$this->_locale = $this->_translate->getLocale();
		}
		$destFile = BASE_PATH . '/var/compile/' . $this->_locale . '_direct_' . $hash . '.php';
		//jeśli włączona kompilacja za każdym razem, nadpisanie pliku
		if ($this->_alwaysCompile) {
			file_put_contents($destFile, $this->template($input, $destFile));
		}
		//próba załadowania kompilanta
		try {
			include $destFile;
		} catch (\Exception $e) {
			//zapis nowego kompilanta jeśli brak
			file_put_contents($destFile, $this->template($input, $destFile));
			include $destFile;
		}
		//przejęcie bufora
		$data = ob_get_contents();
		ob_clean();
		echo $inputBuffer;
		//zwrot z bufora
		\Mmi\Profiler::event('View: ' . $hash . ' rendered');
		return $data;
	}

	/**
	 * Ustawia wyłączenie layoutu
	 * @param boolean $disabled wyłączony
	 * @return \Mmi\View
	 */
	public function setLayoutDisabled($disabled = true) {
		$this->_layoutDisabled = ($disabled === true) ? true : false;
		return $this;
	}

	/**
	 * Czy layout wyłączony
	 * @return boolean
	 */
	public function isLayoutDisabled() {
		return $this->_layoutDisabled;
	}

	/**
	 * Renderuje layout
	 */
	public function renderLayout($module, $controller) {
		//renderowanie layoutu
		return $this->render($this->_getLayout($module, $controller));
	}

	/**
	 * Renderuje szablon z pliku
	 * @param string $fileName nazwa pliku szablonu
	 * @return string zwraca efekt renderowania
	 */
	public function render($fileName) {
		if (!$this->_locale && $this->_translate !== null) {
			$this->_locale = $this->_translate->getLocale();
		}
		//ustalenie adresu kompilanta
		$destFile = BASE_PATH . '/var/compile/' . $this->_locale . '_' . str_replace(['/', '\\', '_Resource_template_'], '_', substr($fileName, strrpos($fileName, '/src') + 5, -4) . '.php');
		if ($this->_alwaysCompile) {
			file_put_contents($destFile, $this->template(file_get_contents($fileName), $destFile));
		}
		try {
			//włączenie kompilanta do kodu
			include $destFile;
		} catch (\Exception $e) {
			//zapis i włączenie
			file_put_contents($destFile, $this->template(file_get_contents($fileName), $destFile));
			include $destFile;
		}
		//przechwycenie danych
		$data = ob_get_contents();
		ob_clean();
		\Mmi\Profiler::event('View: ' . basename($fileName) . ' rendered');
		return $data;
	}

	/**
	 * Pobiera dostępny layout
	 * @param string $module moduł
	 * @param string $controller kontroler
	 * @return string
	 * @throws Exception brak layoutów
	 */
	private function _getLayout($module, $controller) {
		$structure = \Mmi\Controller\Front::getInstance()->getStructure('template');
		//layout dla modułu i kontrolera
		if (isset($structure[$module][$controller]['layout'])) {
			//pobieranie pierwszego jeśli vendor -> local
			return is_array($structure[$module][$controller]['layout']) ? $structure[$module][$controller]['layout'][0] : $structure[$module][$controller]['layout'];
		}
		//layout dla modułu
		if (isset($structure[$module]['layout'])) {
			//pobieranie pierwszego jeśli jest i w vendor i src
			return is_array($structure[$module]['layout']) ? $structure[$module]['layout'][0] : $structure[$module]['layout'];
		}
		//layout aplikacyjny app
		if (isset($structure['app']['layout'])) {
			return $structure['app']['layout'];
		}
		//brak layoutu
		throw new \Exception('Layout not found.');
	}

	/**
	 * Pobiera dostępny template
	 * @param string $module moduł
	 * @param string $controller kontroler
	 * @param string $action akcja
	 * @return string
	 * @throws Exception brak templatów
	 */
	private function _getTemplate($module, $controller, $action) {
		$structure = \Mmi\Controller\Front::getInstance()->getStructure('template');
		if (isset($structure[$module][$controller][$action])) {
			//pobieranie pierwszego jeśli jest i w vendor i src
			return is_array($structure[$module][$controller][$action]) ? $structure[$module][$controller][$action][0] : $structure[$module][$controller][$action];
		}
		//brak template
		throw new \Exception('Template not found.');
	}

}

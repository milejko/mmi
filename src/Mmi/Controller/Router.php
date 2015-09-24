<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Controller;

class Router {

	/**
	 * Konfiguracja
	 * @var \Mmi\Controller\Router\Config
	 */
	private $_config;

	/**
	 * Ścieżka bazowa zapytania
	 * @var string
	 */
	private $_baseUrl;

	/**
	 * Url zapytania
	 * @var string
	 */
	private $_url;

	/**
	 * Domyślny język
	 * @var string
	 */
	private $_defaultLanguage;

	/**
	 *
	 * @param \Mmi\Controller\Router\Config $config
	 * @param string $defaultLanguage domyślny język
	 */
	public function __construct(\Mmi\Controller\Router\Config $config, $defaultLanguage = null) {
		$this->_config = $config;
		$this->_defaultLanguage = $defaultLanguage;
		$this->_url = urldecode(trim(\Mmi\App\FrontController::getInstance()->getEnvironment()->requestUri, '/ '));
		if (false !== $qmarkPosition = strpos($this->_url, '?')) {
			$this->_url = substr($this->_url, 0, $qmarkPosition);
		}
		//obsługa serwisu w podkatalogu
		$subFolderPath = substr(BASE_PATH, strrpos(BASE_PATH, '/') + 1) . '/web';
		$position = strpos($this->_url, $subFolderPath);
		$this->_baseUrl = '';
		if ($position !== false) {
			$this->_baseUrl = substr($this->_url, 0, strlen($subFolderPath) + $position);
			$this->_url = trim(substr($this->_url, strlen($subFolderPath) + $position + 1), '/');
		}
		//wejście przez plik PHP
		if (false !== $scriptPosition = strpos($this->_url, basename(\Mmi\App\FrontController::getInstance()->getEnvironment()->scriptFilename))) {
			$this->_url = substr($this->_url, 0, $scriptPosition);
		}
		$this->_url = rtrim($this->_url, '/');
	}

	/**
	 * Pobiera konfigurację routera
	 * @return \Mmi\Controller\Router\Config
	 */
	public function getConfig() {
		return $this->_config;
	}

	/**
	 * Pobiera trasy
	 * @return array
	 */
	public function getRoutes() {
		return $this->_config->getRoutes();
	}

	/**
	 * Pobiera request po ustawieniu parametrów routingu i danych wejściowych
	 * @return \Mmi\Controller\Request
	 */
	public function processRequest(\Mmi\Controller\Request $request) {
		return $request->setParams($this->decodeUrl($this->_url));
	}

	/**
	 * Dekoduje URL na parametry żądania zgodnie z wczytanymi trasami
	 * @param string $url URL
	 * @return array
	 */
	public function decodeUrl($url) {
		//startowo parametry z GET
		$params = $this->_decodeGet();

		//domyślne parametry
		$params['controller'] = isset($params['controller']) ? $params['controller'] : 'index';
		$params['action'] = isset($params['action']) ? $params['action'] : 'index';

		//jeśli aplikacja jest językowa
		if ($this->_defaultLanguage) {
			$params['lang'] = isset($params['lang']) ? $params['lang'] : $this->_defaultLanguage;
		}

		//jeśli nieustawiony moduł, url nie jest analizowany
		if (isset($params['module'])) {
			return $params;
		}

		//filtrowanie URL
		$filteredUrl = html_entity_decode($url, ENT_HTML401 | ENT_HTML5 | ENT_QUOTES, 'UTF-8');

		//próba aplikacji rout
		foreach ($this->getRoutes() as $route) {
			/* @var $route \Mmi\Controller\Router\Config\Route */
			$result = Router\Matcher::tryRouteForUrl($route, $filteredUrl);
			//dopasowano routę
			if ($result['matched']) {
				//łączenie parametrów
				$params = array_merge($params, $result['params']);
				break;
			}
		}

		//jeśli puste parametry
		if (!isset($params['module']) && $filteredUrl == '') {
			$params['module'] = 'mmi';
		}

		return $params;
	}

	/**
	 * Koduje parametry na URL zgodnie z wczytanymi trasami
	 * @param array $params parametry
	 * @return string
	 */
	public function encodeUrl(array $params = []) {
		//startowo bazowy url aplikacji
		$url = $this->_baseUrl;
		$matched = [];

		//aplikacja rout
		foreach ($this->getRoutes() as $route) {
			/* @var $route \Mmi\Controller\Router\Config\Route */
			$result = Router\Matcher::tryRouteForParams($route, array_merge($route->default, $params));
			//dopasowano routę
			if ($result['applied']) {
				$url .= '/' . $result['url'];
				$matched = $result['matched'];
				break;
			}
		}
		//czyszczenie dopasowanych z routy
		foreach ($matched as $match => $value) {
			unset($params[$match]);
		}
		//czyszczenie modułu jeśli mmi, kontrolera i akcji jeśli index
		$this->_unsetArrayIndexes($params, 'module', 'mmi')
			->_unsetArrayIndexes($params, 'controller')
			->_unsetArrayIndexes($params, 'action');

		//budowanie zapytania
		if ('' != ($query = http_build_query($params))) {
			//zamiana zmiennych tpl
			$url .= '/?' . $query;
		}
		return $url;
	}

	/**
	 * Dekoduje GET na parametry żądania zgodnie z wczytanymi trasami
	 * @return array
	 */
	public function _decodeGet() {
		$params = [];
		foreach ($_GET as $key => $value) {
			$params[$this->filter($key)] = $this->filter($value);
		}
		return $params;
	}

	/**
	 * Pobiera ścieżkę bazową
	 * @return string
	 */
	public function getBaseUrl() {
		return $this->_baseUrl;
	}

	/**
	 * Filtruje string, lub tablicę (w sposób rekurencyjny)
	 * @param mixed $input zmienna wejściowa
	 * @return mixed
	 */
	public function filter($input) {
		if (!is_array($input)) {
			$input = str_replace('&amp;', '&', htmlspecialchars($input));
			if (get_magic_quotes_gpc()) {
				$input = stripslashes($input);
			}
		} elseif (is_array($input)) {
			$newInput = [];
			foreach ($input AS $key => $value) {
				$newInput[$key] = $this->filter($value);
			}
			$input = $newInput;
		}
		return $input;
	}

	/**
	 * Usuwa indeksy z tabeli źródłowej
	 * @param array $params tabela źródłowa
	 * @param string $key klucz w tabeli
	 * @param string $value wartość klucza do usunięcia
	 * @return Router
	 */
	protected function _unsetArrayIndexes(array &$params, $key, $value = 'index') {
		if (!isset($params[$key])) {
			return $this;
		}
		if ($params[$key] == $value) {
			unset($params[$key]);
		}
		return $this;
	}

}

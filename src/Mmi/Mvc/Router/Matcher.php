<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Mvc\Router;

/**
 * Klasa dopasowująca routy
 */
class Matcher {

	//zmienne tymczasowe
	protected static $_tmpMatches;
	protected static $_tmpKey;
	protected static $_tmpDefault;

	/**
	 * Stosuje istniejące trasy dla danego url
	 * @param \Mmi\Mvc\Router\Config\Route $route
	 * @param string $url URL
	 * @return array
	 */
	public static function tryRouteForUrl(\Mmi\Mvc\Router\Config\Route $route, $url) {
		$params = [];
		$matches = [];
		$matched = false;
		//sprawdzenie statyczne
		if ($route->pattern == $url) {
			$matched = true;
			$params = array_merge($route->default, $route->replace);
			return [
				'matched' => true,
				'params' => $params,
				'url' => trim(substr($url, strlen($route->pattern)), ' /')
			];
		}
		//dopasowanie wyrażeniem regularnym
		if (self::_isPatternRegular($route->pattern) && preg_match($route->pattern, $url, $matches)) {
			self::$_tmpMatches = $matches;
			self::$_tmpDefault = $route->default;
			$matched = true;
			foreach ($route->replace as $key => $value) {
				self::$_tmpKey = $key;
				$params[$key] = preg_replace_callback('/\$([0-9]+)/', ['\Mmi\Mvc\Router\Matcher', '_routeMatch'], $value);
				$params[$key] = preg_replace('/\|[a-z]+/', '', $params[$key]);
			}
			$url = trim(substr($url, strlen($matches[0])), ' /');
		}
		return [
			'matched' => $matched,
			'params' => $params,
			'url' => $url
		];
	}

	/**
	 * Stosuje trasę dla tablicy parametrów (np. z żądania)
	 * @param \Mmi\Mvc\Router\Config\Route $route
	 * @param array $params parametry
	 * @return array
	 */
	public static function tryRouteForParams(\Mmi\Mvc\Router\Config\Route $route, array $params) {
		$matches = [];
		$matched = [];
		$applied = true;
		$url = '';
		$replace = array_merge($route->default, $route->replace);
		//routy statyczne tylko ze zgodną liczbą parametrów
		if (!self::_isPatternRegular($route->pattern) && count($replace) != count($params)) {
			return [
				'applied' => false,
				'matched' => $matched,
				'url' => $url
			];
		}
		foreach ($replace as $key => $value) {
			if (is_array($value) && isset($params[$key]) && $value == $params[$key]) {
				$matched[$key] = true;
				continue;
			} elseif (is_array($value)) {
				$applied = false;
				$matched = [];
				break;
			}
			if ((preg_match('/\$([0-9]+)(\|[a-z]+)?/', $value, $mt) && isset($params[$key]))) {
				if (!empty($mt) && count($mt) > 2) {
					$filter = \Mmi\App\FrontController::getInstance()->getView()->getFilter(ucfirst(ltrim($mt[2], '|')));
					$params[$key] = $filter->filter($params[$key]);
				}
				$matches[$value] = $params[$key];
			} elseif (!isset($params[$key]) || $params[$key] != $value) {
				$applied = false;
				$matched = [];
				break;
			}
			$matched[$key] = true;
		}
		if ($applied) {
			$pattern = str_replace(['\\', '?'], '', trim($route->pattern, '/^$'));
			$url = preg_replace('/(\(.[^\)]+\))/', '(#)', $pattern);
			foreach ($matches as $match) {
				if (is_array($match)) {
					$match = trim(implode(';', $match), ';');
				}
				$url = preg_replace('/\(\#\)/', $match, $url, 1);
			}
		}
		return [
			'applied' => $applied,
			'matched' => $matched,
			'url' => $url
		];
	}

	/**
	 * Sprawdzenie czy pattern to wyrażenie regularne
	 * @param string $pattern pattern
	 * @return bool
	 */
	protected static function _isPatternRegular($pattern) {
		return substr($pattern, 0, 1) == '/' && (substr($pattern, -1) == '/' || substr($pattern, -2) == '/i');
	}

	/**
	 * Callback dla zmieniania rout
	 * @param array $matches dopasowania
	 * @return mixed
	 * @throws \Mmi\Mvc\MvcException
	 */
	protected static function _routeMatch($matches) {
		if (!isset($matches[1])) {
			throw new \Mmi\Mvc\MvcException('Router failed due to invalid route definition');
		}
		if (isset(self::$_tmpMatches[$matches[1]])) {
			return self::$_tmpMatches[$matches[1]];
		}
		if (isset(self::$_tmpDefault[self::$_tmpKey])) {
			return self::$_tmpDefault[self::$_tmpKey];
		}
		throw new \Mmi\Mvc\MvcException('Router failed due to invalid route definition - no default param for key: ' . self::$_tmpKey);
	}

}

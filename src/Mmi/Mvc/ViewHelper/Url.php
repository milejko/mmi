<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Mvc\ViewHelper;

class Url extends HelperAbstract {

	/**
	 * Generuje link na podstawie parametrów (z użyciem routera)
	 * @see \Mmi\Mvc\Router::encodeUrl()
	 * @param array $params parametry
	 * @param boolean $reset nie łączy z bieżącym requestem
	 * @param boolean $absolute czy ścieżka bezwzględna
	 * @param boolean | null $https czy wymusić https: tak, nie https, null = bez zmiany protokołu
	 * @param array $unset odłącz parametr
	 * @return string
	 */
	public function url(array $params = [], $reset = false, $absolute = false, $https = null, array $unset = []) {
		if (!$reset) {
			$params = array_merge(\Mmi\App\FrontController::getInstance()->getRequest()->toArray(), $params);
		}
		foreach ($params as $key => $param) {
			if (null === $param) {
				unset($params[$key]);
			}
		}
		foreach ($unset as $key => $param) {
			if (is_int($key) && is_string($param)) {
				unset($params[$param]);
				continue;
			}
			if (is_string($key) && !is_array($param)) {
				unset($params[$key]);
				continue;
			}
			if (!isset($params[$key])) {
				continue;
			}
			if (!is_array($params[$key])) {
				unset($params[$key]);
				continue;
			}
			foreach ($param as $unsetKey) {
				unset($params[$key][$unsetKey]);
			}
			sort($params[$key]);
			if (empty($params[$key])) {
				unset($params[$key]);
			}
		}
		$url = \Mmi\App\FrontController::getInstance()->getRouter()->encodeUrl($params);
		if (!is_null($https)) {
			$absolute = true;
		}
		if ($absolute) {
			$protocol = 'http://';
			if (\Mmi\App\FrontController::getInstance()->getEnvironment()->httpSecure) {
				$protocol = 'https://';
			}
			if (!is_null($https)) {
				if ($https) {
					$protocol = 'https://';
				} else {
					$protocol = 'http://';
				}
			}
			$url = $protocol . \App\Registry::$config->host . $url;
		}
		return $url ? $url : '/';
	}

}

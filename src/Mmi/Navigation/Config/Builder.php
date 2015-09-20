<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Navigation\Config;

class Builder {
	
	/**
	 * Buduje strukturę drzewiastą na podstawie struktury płaskiej
	 * @param array $data
	 * @return array
	 */
	public static function build(array $data = []) {
		$lang = \Mmi\App\FrontController::getInstance()->getRequest()->lang;
		$view = \Mmi\App\FrontController::getInstance()->getView();
		if ($data['disabled'] || ($data['dateStart'] !== null && $data['dateStart'] > date('Y-m-d H:i:s')) || ($data['dateEnd'] !== null && $data['dateEnd'] < date('Y-m-d H:i:s'))) {
			$data['disabled'] = true;
		}
		if (!$data['uri']) {
			$params = $data['params'];
			if ($lang !== null && $data['lang'] !== null) {
				$params['lang'] = $data['lang'];
			}
			$params['module'] = $data['module'];
			$params['controller'] = $data['controller'];
			$params['action'] = $data['action'];
			if ($data['module']) {
				$data['uri'] = $view->url($params, true, $data['absolute'], $data['https']);
				if ($data['module'] == 'cms' && $data['controller'] == 'article' && $data['action'] == 'index') {
					$data['type'] = 'simple';
				} elseif ($data['module'] == 'cms' && $data['controller'] == 'container' && $data['action'] == 'display') {
					$data['type'] = 'container';
				}
			} else {
				$data['uri'] = '#';
				$data['type'] = 'folder';
			}
			$data['request'] = $params;
		} else {
			if (strpos($data['uri'], '://') === false && strpos($data['uri'], '#') !== 0 && strpos($data['uri'], '/') !== 0) {
				$data['uri'] = 'http://' . $data['uri'];
			}
			$data['type'] = 'link';
		}
		$build = $data;
		$build['children'] = [];

		if (!empty($data['children'])) {
			foreach ($data['children'] as $child) {
				$build['children'][$child->getId()] = $child->build();
			}
		}
		return $build;
	}
	
}

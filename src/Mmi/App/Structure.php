<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\App;

class Structure {

	/**
	 * Zwraca dostępne komponenty aplikacji
	 * @return array 
	 */
	public static function getStructure() {
		return array_merge_recursive(self::_parseModules(BASE_PATH . '/src'), self::_analyseVendors());
	}

	/**
	 * Zwraca dostępne komponenty aplikacyjne w systemie
	 * @return array
	 */
	private static function _analyseVendors() {
		$components = ['module' => [],
			'template' => [],
			'translate' => [],
			'helper' => [],
			'filter' => [],
			'validator' => []
		];
		//brak vendorów
		if (!file_exists(BASE_PATH . '/vendor')) {
			return $components;
		}
		foreach (new \DirectoryIterator(BASE_PATH . '/vendor') as $vendor) {
			if (!$vendor->isDir() || $vendor->isDot()) {
				continue;
			}
			foreach (new \DirectoryIterator($vendor->getPathname()) as $vendorName) {
				if (!$vendorName->isDir() || $vendorName->isDot()) {
					continue;
				}
				if (!file_exists($vendorName->getPathname() . '/src')) {
					continue;
				}
				$components = array_merge_recursive(self::_parseModules($vendorName->getPathname() . '/src'), $components);
			}
		}
		return $components;
	}

	/**
	 * Parsuje moduły w katalogu dostawcy lub w źródłach
	 * @param string $path
	 * @return array
	 */
	private static function _parseModules($path) {
		$components = ['module' => [],
			'template' => [],
			'translate' => [],
			'helper' => [],
			'filter' => [],
			'validator' => []
		];
		//brak modułów
		if (!file_exists($path)) {
			return $components;
		}
		foreach (new \DirectoryIterator($path) as $module) {
			if ($module->isDot()) {
				continue;
			}
			//nazwa modułu
			$moduleName = lcfirst(substr($module->getFilename(), strrpos($module->getFilename(), '/')));
			//helpery widoku
			self::_parseAdditions($components['helper'], $module->getFileName(), $module->getPathname() . '/View/Helper');
			//filtry
			self::_parseAdditions($components['filter'], $module->getFileName(), $module->getPathname() . '/Filter');
			//walidatory
			self::_parseAdditions($components['validator'], $module->getFileName(), $module->getPathname() . '/Validate');
			//tłumaczenia
			self::_parseAdditions($components['translate'], $module->getFileName(), $module->getPathname() . '/Resource/i18n');
			//kontrolery
			self::_parseControllers($components['module'], $moduleName, $module->getPathname() . '/Controller');
			//kontrolery
			self::_parseTemplates($components['template'], $moduleName, $module->getPathname() . '/Resource/template');
		}
		return $components;
	}

	/**
	 * Parser kontrolerów
	 * @param array $components
	 * @param string $moduleName
	 * @param string $path
	 */
	private static function _parseTemplates(array &$components, $moduleName, $path) {
		if (!file_exists($path)) {
			return;
		}
		foreach (new \DirectoryIterator($path) as $template) {
			if ($template->isDot()) {
				continue;
			}
			if ($template->isFile()) {
				$components[$moduleName][substr($template->getFilename(), 0, -4)] = $template->getPathname();
				continue;
			}
			if (!$template->isDir()) {
				continue;
			}
			foreach (new \DirectoryIterator($template->getPathname()) as $action) {
				if ($action->isDot()) {
					continue;
				}
				if ($action->isFile()) {
					$components[$moduleName][$template->getFilename()][substr($action->getFilename(), 0, -4)] = $action->getPathname();
					continue;
				}
				$components[$moduleName][$template->getFilename()][substr($action->getFilename(), 0, -4)] = $action->getPathname();
			}
		}
	}

	/**
	 * Parser kontrolerów
	 * @param array $components
	 * @param string $moduleName
	 * @param string $path
	 */
	private static function _parseControllers(array &$components, $moduleName, $path) {
		if (!file_exists($path)) {
			return;
		}
		foreach (new \DirectoryIterator($path) as $controller) {
			if ($controller->isDot()) {
				continue;
			}
			$controllerName = lcfirst(substr($controller->getFilename(), 0, -4));
			//parsuje akcje z kontrolera
			self::_parseActions($components, $controller->getPathname(), $moduleName, $controllerName);
		}
	}

	/**
	 * Parsowanie akcji w kontrolerze
	 * @param array $components
	 * @param string $controllerPath
	 * @param string $moduleName
	 * @param string $controllerName
	 */
	private static function _parseActions(array &$components, $controllerPath, $moduleName, $controllerName) {
		//łapanie nazw akcji w kodzie
		if (preg_match_all('/function ([a-zA-Z0-9]+Action)\(/', file_get_contents($controllerPath), $actions)) {
			foreach ($actions[1] as $action) {
				$components[$moduleName][$controllerName][substr($action, 0, -6)] = 1;
			}
		}
	}

	/**
	 * Zwraca dostępne helpery i filtry w bibliotekach
	 */
	private static function _parseAdditions(array &$components, $namespace, $path) {
		if (!file_exists($path)) {
			return;
		}
		foreach (new \DirectoryIterator($path) as $object) {
			if ($object->isDot() || $object->isDir()) {
				continue;
			}
			$components[$namespace][lcfirst(substr($object->getFilename(), 0, -4))] = substr($object->getFilename(), -3) == 'php' ? 1 : $object->getPathname();
		}
	}

}

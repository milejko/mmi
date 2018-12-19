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
 * Klasa struktury MVC
 */
class Structure
{

    /**
     * Zwraca dostępne komponenty aplikacji
     * @return array 
     */
    public static function getStructure()
    {
        $components = [
            'module' => [],
            'template' => [],
            'translate' => [],
            'filter' => [],
            'helper' => [],
        ];
        //vendors na koniec
        foreach (array_reverse(\Mmi\Mvc\StructureParser::getModules()) as $module) {
            $components = array_merge_recursive(self::_parseModule($module), $components);
        }
        return $components;
    }

    /**
     * Parsuje moduły w katalogu dostawcy lub w źródłach
     * @param string $path
     * @return array
     */
    private static function _parseModule($path)
    {
        $components = [
            'module' => [],
            'template' => [],
            'translate' => [],
            'filter' => [],
            'helper' => [],
        ];
        //brak modułów
        if (!file_exists($path)) {
            return $components;
        }
        $module = basename($path);
        //helpery widoku
        self::_parseAdditions($components['helper'], $module, $path . '/Mvc/ViewHelper');
        //filtry
        self::_parseAdditions($components['filter'], $module, $path . '/Filter');
        //tłumaczenia
        self::_parseAdditions($components['translate'], lcfirst($module), $path . '/Resource/i18n');
        //kontrolery
        self::_parseControllers($components['module'], lcfirst($module), $path);
        //szablony
        $components['template'][lcfirst($module)] = [];
        self::_parseTemplates($components['template'][lcfirst($module)], $path . '/Resource/template');
        return $components;
    }

    /**
     * Parser kontrolerów
     * @param array $components
     * @param string $moduleName
     * @param string $path
     */
    private static function _parseTemplates(array &$components, $path)
    {
        if (!file_exists($path)) {
            return;
        }
        foreach (new \DirectoryIterator($path) as $template) {
            if ($template->isDot()) {
                continue;
            }
            if ($template->isFile()) {
                $components[substr($template->getFilename(), 0, -4)] = $template->getPathname();
                continue;
            }
            if ($template->isDir()) {
                $components[$template->getFilename()] = [];
                self::_parseTemplates($components[$template->getFilename()], $template->getPathname());
            }
        }
    }

    /**
     * Parser kontrolerów
     * @param array $components
     * @param string $moduleName
     * @param string $path
     */
    private static function _parseControllers(array &$components, $moduleName, $path)
    {
        if (!file_exists($path)) {
            return;
        }
        foreach (new \DirectoryIterator($path) as $controller) {
            if ($controller->isDot()) {
                continue;
            }
            //plik nie jest kontrolerem
            if (false === strpos($controller->getFilename(), 'Controller')) {
                continue;
            }
            $controllerName = lcfirst(substr($controller->getFilename(), 0, -14));
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
    private static function _parseActions(array &$components, $controllerPath, $moduleName, $controllerName)
    {
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
    private static function _parseAdditions(array &$components, $namespace, $path)
    {
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

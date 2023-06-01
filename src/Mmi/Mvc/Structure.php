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
    public static function getStructure($object = null)
    {
        $components = [
            'module' => [],
            'template' => [],
            'translate' => [],
            'filter' => [],
            'helper' => [],
            'di'     => [],
        ];
        //pobiera moduły
        foreach (self::getModules() as $module) {
            $components = array_merge_recursive(self::_parseModule($module, $object), $components);
        }
        return $object ? $components[$object] : $components;
    }

    public static function getModules(): array
    {
        return StructureParser::getModules();
    }

    /**
     * Parsuje moduły w katalogu dostawcy lub w źródłach
     * @param string $path
     * @return array
     */
    private static function _parseModule($path, $object = null)
    {
        $components = [
            'classes' => [],
            'template' => [],
            'translate' => [],
            'di'     => [],
        ];
        //module does not exist
        if (!file_exists($path)) {
            return $components;
        }
        $module = basename($path);
        //dependency injection
        (!$object || 'di' == $object) && self::_parseFiles($components['di'], $path . '/di.*php');
        //view helpers
        (!$object || 'classes' == $object) && self::_parseClasses($components['classes'], $module . '\\Mvc\\ViewHelper', $path . '/Mvc/ViewHelper/*.php', 'ViewHelper');
        //filters
        (!$object || 'classes' == $object) && self::_parseClasses($components['classes'], $module . '\\Filter', $path . '/Filter/*.php', 'Filter');
        //controllers
        (!$object || 'classes' == $object) && self::_parseClasses($components['classes'], $module, $path . '/*Controller.php');
        //commands
        (!$object || 'classes' == $object) && self::_parseClasses($components['classes'], $module . '\\Command', $path . '/Command/*Command.php');
        //translate
        (!$object || 'translate' == $object) && self::_parseFiles($components['translate'], $path . '/Resource/i18n/*.ini');
        //templates
        $components['template'][\lcfirst($module)] = [];
        (!$object || 'template' == $object) && self::_parseTemplates($components['template'][\lcfirst($module)], $path . '/Resource/template');
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
     * Zwraca dostępne helpery i filtry w bibliotekach
     */
    private static function _parseClasses(array &$components, $namespace, $path, $namespaceAlias = null)
    {
        foreach (glob($path) as $file) {
            $classCode = file_get_contents($file);
            //making sure that class is not an abstract
            //@TODO: change to reflection
            if (\strpos($classCode, 'abstract class')) {
                continue;
            }
            $objectName = substr(basename($file), 0, -4);
            if (!class_exists($namespace . '\\' . $objectName)) {
                continue;
            }
            $components[($namespaceAlias ?: $namespace) . '\\' . $objectName] = $namespace . '\\' . $objectName;
        }
    }

    /**
     * Parses DI configuration files
     */
    private static function _parseFiles(array &$components, $path)
    {
        foreach (glob($path) as $file) {
            $components[] = $file;
        }
    }
}

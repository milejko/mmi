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
 * Klasa parsera struktury MVC
 */
class StructureParser
{

    /**
     * Katalogi konieczne w module
     * @var array
     */
    protected static $_moduleRequirements = [
        'IndexController.php',
        'WidgetController.php',
        'Resource',
        'Filter',
        'Model',
        'Orm',
        'Resource',
        'View',
        'Form'
    ];

    /**
     * Pobiera wszystkie moduły w aplikacji
     * @return array
     */
    public static function getModules()
    {
        return array_merge(self::getSrcModules(), self::getVendorModules());
    }

    /**
     * Pobieranie modułów aplikacyjnych z src
     * @return array
     */
    public static function getSrcModules()
    {
        $modules = [];
        foreach (new \DirectoryIterator(BASE_PATH . 'src') as $module) {
            if (!$module->isDir() || $module->isDot() || !self::_moduleValid($module->getPathname())) {
                continue;
            }
            $modules[] = $module->getPathname();
        }
        return $modules;
    }

    /**
     * Pobranie modułów aplikacyjnych ze wszystkich vendorów
     * @return array
     */
    public static function getVendorModules()
    {
        $modules = [];
        //iteracja po vendorach
        foreach (self::getVendors() as $vendor) {
            //iteracja po modułach
            foreach (new \DirectoryIterator($vendor) as $module) {
                if (!$module->isDir() || $module->isDot() || !self::_moduleValid($module->getPathname())) {
                    continue;
                }
                $modules[] = $module->getPathname();
            }
        }
        return $modules;
    }

    /**
     * Zwraca dostępne moduły aplikacyjne w vendorach
     * @return array
     */
    public static function getVendors()
    {
        $vendors = [];
        //brak vendorów
        if (!file_exists(BASE_PATH . 'vendor')) {
            return $vendors;
        }
        foreach (new \DirectoryIterator(BASE_PATH . 'vendor') as $vendor) {
            if (!$vendor->isDir() || $vendor->isDot()) {
                continue;
            }
            foreach (new \DirectoryIterator($vendor->getPathname()) as $vendorName) {
                if (!$vendorName->isDir() || $vendorName->isDot() || !file_exists($vendorName->getPathname() . '/src')) {
                    continue;
                }
                $vendors[] = $vendorName->getPathname() . '/src';
            }
        }
        return $vendors;
    }

    /**
     * Bada poprawność modułu
     * @param string $modulePath
     * @return boolean
     */
    protected static function _moduleValid($modulePath)
    {
        //iteracja po wymaganych katalogach
        foreach (self::$_moduleRequirements as $req) {
            //sprawdzanie istnienia katalogu
            if (file_exists($modulePath . '/' . $req)) {
                return true;
            }
        }
        return false;
    }

}

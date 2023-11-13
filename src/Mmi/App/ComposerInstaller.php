<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\App;

use Composer\Script\Event;
use Mmi\FileSystem;

/**
 * Klasa używana przy instalacji composerem
 */
class ComposerInstaller
{
    /**
     * Katalogi systemowe
     * @var array
     */
    protected static $sysDirs = ['bin', 'var/cache', 'var/data', 'var/log', 'var/session', 'web/data', 'web/resource'];

    /**
     * Inicjalizacja autoloadera
     * @param Event $event
     */
    protected static function initAutoload(Event $event)
    {
        //określenie katalogu vendorów
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        //ustawianie ścieżki bazowej projektu
        define('BASE_PATH', realpath($vendorDir . '/../'));
        //wczytanie autoloadera
        require $vendorDir . '/autoload.php';
    }

    /**
     * Po aktualizacji
     * @param Event $event
     */
    public static function postUpdate(Event $event)
    {
        self::postInstall($event);
    }

    /**
     * Po instalacji
     * @param Event $event
     */
    public static function postInstall(Event $event)
    {
        //inicjalizacja autoloadera
        self::initAutoload($event);
        //czyszczenie web/resource
        FileSystem::rmdirRecursive(BASE_PATH . '/web/resource');
        //tworzenie katalogów systemowych
        self::createSysDirs();
        //kopiowanie binariów z modułów
        self::copyExecutables();
        //w trybie deweloperskim linkowanie zasobów i routera dla php -S
        if ($event->isDevMode()) {
            self::linkModuleWebResources();
            copy(BASE_PATH . '/vendor/mmi/mmi/src/Mmi/App/executables/php-serve-router.php', BASE_PATH . '/web/php-serve-router.php');
            return;
        }
        //kopiowanie zasobów do web/data
        self::copyModuleWebResources();
    }

    /**
     * Tworzenie katalogów
     */
    protected static function createSysDirs()
    {
        //iteracja po katalogach obowiązkowych
        foreach (self::$sysDirs as $dir) {
            //tworzenie katalogu
            !file_exists(BASE_PATH . '/' . $dir) ? mkdir(BASE_PATH . '/' . $dir, 0777, true) : null;
            chmod($dir, 0777);
        }
    }

    /**
     * Linkuje zasoby publiczne do /web
     */
    public static function linkModuleWebResources()
    {
        //iteracja po modułach
        foreach (\Mmi\Mvc\StructureParser::getModules() as $module) {
            //kalkulacja ścieżki linku
            $linkName = BASE_PATH . '/web/resource/' . lcfirst(basename($module));
            //istnieje resource web
            if (file_exists($module . '/Resource/web')) {
                //tworzenie linku
                symlink(realpath($module . '/Resource/web'), $linkName);
            }
        }
    }

    /**
     * Kopiuje zasoby publiczne do /web
     */
    public static function copyModuleWebResources()
    {
        //iteracja po modułach
        foreach (\Mmi\Mvc\StructureParser::getModules() as $module) {
            //kalkulacja ścieżki linku
            $dirName = BASE_PATH . '/web/resource/' . lcfirst(basename($module));
            //istnieje resource web
            if (file_exists($module . '/Resource/web')) {
                //tworzenie linku
                FileSystem::copyRecursive($module . '/Resource/web', $dirName);
            }
        }
    }

    /**
     * Kopiuje binaria do /bin
     */
    protected static function copyExecutables()
    {
        copy(BASE_PATH . '/vendor/mmi/mmi/src/Mmi/App/executables/index.php', BASE_PATH . '/web/index.php');
        copy(BASE_PATH . '/vendor/mmi/mmi/src/Mmi/App/executables/mmi', BASE_PATH . '/bin/mmi');
        chmod(BASE_PATH . '/bin/mmi', 0755);
    }
}

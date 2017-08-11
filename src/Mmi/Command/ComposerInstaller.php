<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Command;

use Composer\Script\Event;

/**
 * Klasa używana przy instalacji composerem
 */
class ComposerInstaller
{

    /**
     * Pliki dystrybucyjne
     * @var array
     */
    protected static $_distFiles = [
        'dist' => '',
    ];

    /**
     * Karalogi systemowe
     * @var array
     */
    protected static $_sysDirs = ['bin', 'var/cache', 'var/compile', 'var/data', 'var/log', 'var/session', 'web/data', 'web/resource'];

    /**
     * Po aktualizacji
     * @param Event $event
     */
    public static function postUpdate(Event $event)
    {
        //inicjalizacja autoloadera
        self::_initAutoload($event);
        //linkowanie zasobów web
        self::_linkModuleWebResources();
        //kopiowanie binariów z modułów
        self::_copyModuleBinaries();
    }

    /**
     * Po instalacji
     * @param Event $event
     */
    public static function postInstall(Event $event)
    {
        //inicjalizacja autoloadera
        self::_initAutoload($event);
        //kopiowanie plików z dystrybucji
        self::_copyDistFiles();
        //linkowanie zasobów web
        self::_linkModuleWebResources();
        //kopiowanie binariów z modułów
        self::_copyModuleBinaries();
    }

    /**
     * Inicjalizacja autoloadera
     * @param Event $event
     */
    protected static function _initAutoload(Event $event)
    {
        //określenie katalogu vendorów
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        //ustawianie ścieżki bazowej projektu
        define('BASE_PATH', $vendorDir . '/../');
        //kopiowanie plików dist
        self::_createSysDirs();
        self::_copyDistFiles();
        //wczytanie autoloadera
        require $vendorDir . '/autoload.php';
    }

    /**
     * Tworzenie katalogów
     */
    protected static function _createSysDirs()
    {
        //iteracja po katalogach obowiązkowych
        foreach (self::$_sysDirs as $dir) {
            //tworzenie katalogu
            !file_exists(BASE_PATH . '/' . $dir) ? mkdir(BASE_PATH . '/' . $dir, 0777, true) : null;
            chmod($dir, 0777);
        }
    }

    /**
     * Kopiuje pliki z dystrybucji
     */
    protected static function _copyDistFiles()
    {
        //iteracja po wymaganych plikach
        foreach (self::$_distFiles as $src => $dest) {
            //kalkulacja ścieżki
            $source = BASE_PATH . $src;
            //brak pliku
            if (!file_exists($source)) {
                continue;
            }
            //kopiowanie katalogów
            \Mmi\FileSystem::copyRecursive($source, BASE_PATH . $dest, false);
            //usuwanie źródła
            \Mmi\FileSystem::rmdirRecursive($source);
            //usuwanie placeholderów
            \Mmi\FileSystem::unlinkRecursive('.placeholder', BASE_PATH . $dest);
        }
    }

    /**
     * Linkuje zasoby publiczne do /web
     */
    protected static function _linkModuleWebResources()
    {
        //iteracja po modułach
        foreach (\Mmi\Mvc\StructureParser::getModules() as $module) {
            //kalkulacja ścieżki linku
            $linkName = BASE_PATH . '/web/resource/' . lcfirst(basename($module));
            //link istnieje
            if (is_link($linkName)) {
                continue;
            }
            //czyszczenie katalogu który ma być linkiem
            if (file_exists($linkName)) {
                //usuwanie kaskadowe
                \Mmi\FileSystem::rmdirRecursive($linkName);
            }
            //istnieje resource web
            if (file_exists($module . '/Resource/web')) {
                //tworzenie linku
                symlink(realpath($module . '/Resource/web'), $linkName);
            }
        }
    }

    /**
     * Kopiuje binaria do /bin
     */
    protected static function _copyModuleBinaries()
    {
        //iteracja po modułach
        foreach (\Mmi\Mvc\StructureParser::getModules() as $module) {
            //istnieje resource web
            if (file_exists($module . '/Command')) {
                //kopiowanie kaskadowe
                \Mmi\FileSystem::copyRecursive($module . '/Command', BASE_PATH . '/bin');
            }
        }
        //iteracja po binarkach
        foreach (new \DirectoryIterator(BASE_PATH . '/bin') as $file) {
            //katalog (lub wyjście wyżej)
            if ($file->isDot() || $file->isDir()) {
                continue;
            }
            //zmiana uprawnień
            chmod($file->getPathname(), 0755);
        }
    }

}

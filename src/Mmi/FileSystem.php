<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi;

/**
 * Klasa obsługująca system plików
 */
class FileSystem
{

    /**
     * Kopiowanie rekursywne
     * @param string $src
     * @param string $dest
     * @return boolean
     */
    public static function copyRecursive($src, $dest, $overwrite = true)
    {
        try {
            $dir = opendir($src);
        } catch (\Mmi\App\KernelException $e) {
            return false;
        }
        //brak pliku
        if (!file_exists($dest)) {
            mkdir($dest);
        }
        //odczyt katalogu
        while (false !== ($file = readdir($dir))) {
            //plik to wyście "w górę"
            if ($file == '.' || $file == '..') {
                continue;
            }
            //jeśli katalog
            if (is_dir($src . '/' . $file)) {
                //zejście rekurencyjne
                self::copyRecursive($src . '/' . $file, $dest . '/' . $file);
            } elseif (!file_exists($src . '/' . $file) || $overwrite) {
                //kopiowanie
                copy($src . '/' . $file, $dest . '/' . $file);
            }
        }
        //zamknięcie katalogu
        closedir($dir);
        return true;
    }

    /**
     * Kasuje pliki rekurencyjnie
     * @param string $fileName nazwa pliku
     * @param string $rootName katalog główny
     * @return boolean
     */
    public static function unlinkRecursive($fileName, $rootName)
    {
        //brak katalogu głównego
        if (!file_exists($rootName)) {
            return false;
        }
        //iteracja po katalogu głównym
        foreach (new \DirectoryIterator($rootName) as $file) {
            //katalog .
            if ($file->isDot()) {
                continue;
            }
            //katalog
            if ($file->isDir()) {
                //zejście rekurencyjne
                self::unlinkRecursive($fileName, $file->getPathname());
                continue;
            }
            //to nie jest szukany plik
            if ($fileName != $file->getFilename()) {
                continue;
            }
            //próba usunięcia
            try {
                unlink($file->getPathname());
            } catch (\Exception $e) {
                //nic
            }
        }
        return true;
    }

    /**
     * Usuwa katalog rekurencyjnie
     * @param string $dirName nazwa katalogu
     * @return boolean
     */
    public static function rmdirRecursive($dirName)
    {
        //próba zbadania czy jest plikiem
        try {
            $isFile = is_file($dirName);
        } catch (\Exception $e) {
            return false;
        }
        //zwykły plik
        if ($isFile) {
            //próba usunięcia
            try {
                unlink($dirName);
            } catch (\Exception $e) {
                //prawdopodobnie już usunięty
                return false;
            }
            return true;
        }
        //próba otwarcia katalogu
        try {
            $directoryIterator = new \DirectoryIterator($dirName);
        } catch (\Exception $e) {
            //prawdopodobnie już usunięty
            return false;
        }
        //iteracja po katalogu
        foreach ($directoryIterator as $dir) {
            //katalog .
            if ($dir->isDot()) {
                continue;
            }
            //usunięcie rekurencyjne
            self::rmdirRecursive($dir->getPathname());
        }
        //próba usunięcia pustego katalogu
        try {
            rmdir($dirName);
        } catch (\Exception $e) {
            //prawdopodobnie już usunięty
            return false;
        }
        return true;
    }

    /**
     * Zwraca mimetype pliku
     * @param string $fileAddress adres pliku
     * @return string
     */
    public static function mimeType($fileAddress)
    {
        //zwrot informacji o mime
        return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $fileAddress);
    }

    /**
     * Zwraca mimetype pliku binarnego
     * @param string $binary plik binarny
     * @return string
     */
    public static function mimeTypeBinary($binary)
    {
        //zwrot informacji o mime binarium
        return finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $binary);
    }

}

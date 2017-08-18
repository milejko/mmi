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
     */
    public static function copyRecursive($src, $dest, $overwrite = true)
    {
        $dir = opendir($src);
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
    }

    /**
     * Kasuje pliki rekurencyjnie
     * @param string $fileName nazwa pliku
     * @param string $rootName katalog główny
     */
    public static function unlinkRecursive($fileName, $rootName)
    {
        //brak katalogu głównego
        if (!file_exists($rootName)) {
            return;
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
            //szukany plik
            if ($fileName == $file->getFilename()) {
                //usuwanie               
                unlink($file->getPathname());
            }
        }
    }

    /**
     * Usuwa katalog rekurencyjnie
     * @param string $dirName nazwa katalogu
     */
    public static function rmdirRecursive($dirName)
    {
        //nie istnieje
        if (!file_exists($dirName)) {
            return false;
        }
        //zwykły plik
        if (is_file($dirName)) {
            unlink($dirName);
            return true;
        }
        //iteracja po katalogu
        foreach (new \DirectoryIterator($dirName) as $dir) {
            //katalog .
            if ($dir->isDot()) {
                continue;
            }
            //usunięcie rekurencyjne
            self::rmdirRecursive($dir->getPathname());
        }
        //usunięcie pustego katalogu
        rmdir($dirName);
        return true;
    }

    /**
     * Zwraca mimetype pliku
     * @param string $fileAddress adres pliku
     * @return string
     */
    public static function mimeType($fileAddress)
    {
        //brak rozszerzenia fileinfo
        if (!\extension_loaded('fileinfo')) {
            throw new \Mmi\App\KernelException('Fileinfo plugin not installed');
        }
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
        //brak rozszerzenia fileinfo
        if (!function_exists('finfo_open')) {
            throw new \Mmi\App\KernelException('Fileinfo plugin not installed');
        }
        //zwrot informacji o mime binarium
        return finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $binary);
    }

}

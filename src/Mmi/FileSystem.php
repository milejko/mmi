<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi;

/**
 * Klasa obsługująca system plików
 */
class FileSystem {

	/**
	 * Kopiowanie rekursywne
	 * @param string $src
	 * @param string $dest
	 */
	public static function copyRecursive($src, $dest, $overwrite = true) {
		$dir = opendir($src);
		//brak pliku
		if (!file_exists($dest)) {
			mkdir($dest);
		}
		while (false !== ($file = readdir($dir))) {
			if ($file == '.' || $file == '..') {
				continue;
			}
			if (is_dir($src . '/' . $file)) {
				self::copyRecursive($src . '/' . $file, $dest . '/' . $file);
			} elseif (!file_exists($src . '/' . $file) || $overwrite) {
				copy($src . '/' . $file, $dest . '/' . $file);
			}
		}
		closedir($dir);
	}

	/**
	 * Kasuje pliki rekurencyjnie
	 * @param string $fileName nazwa pliku
	 * @param string $rootName katalog główny
	 */
	public static function unlinkRecursive($fileName, $rootName) {
		if (!file_exists($rootName)) {
			return;
		}
		foreach (new \DirectoryIterator($rootName) as $file) {
			//katalog .
			if ($file->isDot()) {
				continue;
			}
			//katalog - rekursja
			if ($file->isDir()) {
				self::unlinkRecursive($fileName, $file->getPathname());
				continue;
			}
			//szukany plik - usuwanie
			if ($fileName == $file->getFilename()) {
				die($file->getPathname());
				unlink($file->getPathname());
			}
		}
	}

	/**
	 * Usuwa katalog rekurencyjnie
	 * @param string $dirName nazwa katalogu
	 */
	public static function rmdirRecursive($dirName) {
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
	public static function mimeType($fileAddress) {
		if (!function_exists('finfo_open')) {
			throw new\Exception('Fileinfo plugin not installed');
		}
		return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $fileAddress);
	}

	/**
	 * Zwraca mimetype pliku binarnego
	 * @param string $binary plik binarny
	 * @return string
	 */
	public static function mimeTypeBinary($binary) {
		if (!function_exists('finfo_open')) {
			throw new\Exception('Fileinfo plugin not installed');
		}
		return finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $binary);
	}

}

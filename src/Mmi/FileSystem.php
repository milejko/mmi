<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi;

/**
 * Klasa obsługująca system plików
 */
class FileSystem {

	/**
	 * Kasuje pliki rekurencyjnie
	 * @param string $fileName nazwa pliku
	 * @param string $rootName katalog główny
	 */
	public static function unlinkRecursive($fileName, $rootName) {
		if (!file_exists($rootName)) {
			return;
		}
		foreach (glob($rootName . '/*') as $file) {
			if ($fileName == basename($file) && is_file($file)) {
				unlink($file);
				continue;
			}
			if (is_dir($file)) {
				self::unlinkRecursive($fileName, $file);
			}
		}
	}

	/**
	 * Usuwa katalog rekurencyjnie
	 * @param string $dirName nazwa katalogu
	 */
	public static function rmdirRecursive($dirName) {
		if (!file_exists($dirName)) {
			return false;
		}
		foreach (glob($dirName . '/*') as $file) {
			if (is_file($file)) {
				unlink($file);
				continue;
			}
			if (is_dir($file)) {
				self::rmdirRecursive($file);
			}
		}
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

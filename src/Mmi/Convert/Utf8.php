<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */
namespace \Mmi\Convert;

class Utf8 {

	/**
	 * Zwraca charakter dla indeksu w utf-8
	 * @param int $ord wartość liczbowa
	 * @return string
	 */
	public static function utf8Chr($ord) {
		return mb_convert_encoding(pack('n', $ord), 'UTF-8', 'UTF-16BE');
	}

	/**
	 * Zwraca indeks dla charakteru w utf-8
	 * @param char $chr charakter
	 * @return int | bool false jeśli poza zakresem utf-8
	 */
	public static function utf8Ord($chr) {
		if (strlen($chr) == 1) {
			return ord($chr);
		}
		$h = ord($chr{0});

		if ($h <= 0x7F) {
			return $h;
		}
		if ($h < 0xC2) {
			return false;
		}
		if ($h <= 0xDF) {
			return ($h & 0x1F) << 6 | (ord($chr{1}) & 0x3F);
		}
		if ($h <= 0xEF) {
			return ($h & 0x0F) << 12 | (ord($chr{1}) & 0x3F) << 6 | (ord($chr{2}) & 0x3F);
		}
		if ($h <= 0xF4) {
			return ($h & 0x0F) << 18 | (ord($chr{1}) & 0x3F) << 12 | (ord($chr{2}) & 0x3F) << 6 | (ord($chr{3}) & 0x3F);
		}
		return false;
	}

}

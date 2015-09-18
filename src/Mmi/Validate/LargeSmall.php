<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Validate;

class LargeSmall extends ValidateAbstract {

	/**
	 * Komunikat o zbyt dużej ilości wielkich liter
	 */
	const INVALID = 'Tekst zawiera zbyt dużo wielkich liter';

	/**
	 * Waliduje zawartość wielkich liter, ilość procent zadana jest w opcjach (przy konstruktorze)
	 * w tabeli postaci array(procent)
	 * @param string $value
	 * @return boolean
	 */
	public function isValid($value) {
		if (strlen($value) == 0) {
			return;
		}
		$percent = isset($this->_options[0]) ? $this->_options[0] : 40;
		$percent = $percent / 100;
		$largeCount = 0;
		if (mb_detect_encoding($value) != '') {
			$upper = mb_strtoupper($value, mb_detect_encoding($value));
		} else {
			$upper = strtoupper($value);
		}
		for ($i = 0, $len = strlen($value); $i < $len; $i++) {
			if (isset($value[$i]) && isset($upper[$i]) && !is_numeric($value[$i]) &&
				$value[$i] != ' ' &&
				$value[$i] != '.' &&
				$value[$i] != ',' &&
				$value[$i] != '!' &&
				$value[$i] != '?' &&
				$value[$i] != '@' &&
				$value[$i] != '%' &&
				$value[$i] != '&' &&
				$value[$i] != '(' &&
				$value[$i] != ')' &&
				$value[$i] != ']' &&
				$value[$i] != '[' &&
				$value[$i] != ':' &&
				$value[$i] != ';' &&
				$value[$i] != '/' &&
				$value[$i] != '+' &&
				$value[$i] != '-' &&
				$value[$i] != '`' &&
				$value[$i] != '$' &&
				$upper[$i] == $value[$i]) {
				$largeCount++;
			}
		}
		if (($largeCount / $len) > $percent) {
			$this->_error(self::INVALID);
			return false;
		}
		return true;
	}

}

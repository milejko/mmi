<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Filter;

class Iban extends \Mmi\Filter\FilterAbstract {

	/**
	 * Poprawia wygląd numerów IBAN
	 * @param mixed $value wartość
	 * @throws Exception jeśli filtrowanie $value nie jest możliwe
	 * @return mixed
	 */
	public function filter($value) {
		$defaultCountry = isset($this->_options[0]) ? $this->_options[0] : 'PL';
		$useSpaces = isset($this->_options[1]) ? $this->_options[1] : true;
		$trims = [' ', '-', '_', '.', ',', '/', '|']; //znaki do usuniącia
		$tmp = strtoupper(str_replace($trims, '', $value));
		if (!isset($tmp[0])) {
			return $value;
		}
		if (is_numeric($tmp[0])) {
			$tmp = 'PL' . $tmp;
		}
		if (!$useSpaces) {
			return $tmp;
		}
		$value = '';
		for ($i = 0, $len = strlen($tmp); $i < $len; $i++) {
			if (($i % 4) == 0 && $i != 0) {
				$value .= ' ';
			}
			$value .= $tmp[$i];
		}
		return $value;
	}

}

<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Filter;

/**
 * Bada długość ciągu lub tabeli
 */
class Length extends \Mmi\Filter\FilterAbstract {

	/**
	 * Zliczanie długości
	 * @param mixed $value wartość
	 * @throws \Mmi\App\KernelException jeśli filtrowanie $value nie jest możliwe
	 * @return int
	 */
	public function filter($value) {
		//string lub numer
		if (is_string($value) || is_numeric($value)) {
			return mb_strlen((string) $value, mb_detect_encoding($value));
		}
		//array
		if (is_array($value) || $value instanceof \ArrayObject) {
			return count($value);
		}
	}

}

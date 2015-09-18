<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Filter;

class Length extends \Mmi\Filter\FilterAbstract {

	/**
	 * Zliczanie długości
	 * @param mixed $value wartość
	 * @throws Exception jeśli filtrowanie $value nie jest możliwe
	 * @return int
	 */
	public function filter($value) {

		if (is_string($value) || is_numeric($value)) {
			return mb_strlen((string) $value);
		}
		if (is_array($value) || $value instanceof \ArrayObject) {
			return count($value);
		}
	}

}

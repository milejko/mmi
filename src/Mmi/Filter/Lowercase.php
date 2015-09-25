<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Filter;

class Lowercase extends \Mmi\Filter\FilterAbstract {

	/**
	 * Zmniejsza wszystkie litery w ciągu
	 * @param mixed $value wartość
	 * @throws \Mmi\App\Exception jeśli filtrowanie $value nie jest możliwe
	 * @return mixed
	 */
	public function filter($value) {
		return mb_strtolower($value, mb_detect_encoding($value));
	}

}

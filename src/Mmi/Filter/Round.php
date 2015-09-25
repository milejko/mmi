<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Filter;

class Round extends \Mmi\Filter\FilterAbstract {

	/**
	 * Zaokrągla liczby
	 * @param mixed $value wartość
	 * @throws \Mmi\App\Exception jeśli filtrowanie $value nie jest możliwe
	 * @return mixed
	 */
	public function filter($value) {
		$precision = (int) (isset($this->_options[0]) ? $this->_options[0] : 0);
		return round($value, $precision);
	}

}

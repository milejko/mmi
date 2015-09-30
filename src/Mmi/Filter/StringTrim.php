<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Filter;

/**
 * Obcięcie
 */
class StringTrim extends \Mmi\Filter\FilterAbstract {

	/**
	 * Usuwa spacę z końców ciągu znaków
	 * @param mixed $value wartość
	 * @throws \Mmi\App\KernelException jeśli filtrowanie $value nie jest możliwe
	 * @return mixed
	 */
	public function filter($value) {
		$chars = ' ';
		if (isset($this->_options[0])) {
			$chars = ' ' . $this->_options[0];
		}
		return trim($value, $chars);
	}

}

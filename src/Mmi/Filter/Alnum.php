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
 * Filtr alfanumeryczny
 */
class Alnum extends \Mmi\Filter\FilterAbstract {

	/**
	 * Zwraca przefiltrowaną wartość
	 * @param mixed $value wartość
	 * @throws \Mmi\App\KernelException jeśli filtrowanie $value nie jest możliwe
	 * @return mixed
	 */
	public function filter($value) {
		return preg_replace('/[^\p{L}\p{N}]/u', '', $value);
	}

}

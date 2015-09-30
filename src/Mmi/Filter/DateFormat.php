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
 * Formater dat
 */
class DateFormat extends \Mmi\Filter\FilterAbstract {

	/**
	 * Filtracja dat
	 * @param mixed $value wartość
	 * @throws \Mmi\App\KernelException jeśli filtrowanie $value nie jest możliwe
	 * @return mixed
	 */
	public function filter($value) {
		$format = isset($this->_options[0]) ? $this->_options[0] : 'd.m.Y H:i:s';
		$timestamp = $value;
		if (!is_numeric($value)) {
			$timestamp = strtotime($value);
		}
		return date($format, $timestamp);
	}

}

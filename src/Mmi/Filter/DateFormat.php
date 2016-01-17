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
 * 
 * @method self setFormat($format) ustawia format
 * @method string getFormat()
 */
class DateFormat extends \Mmi\Filter\FilterAbstract {

	/**
	 * Ustawia opcje
	 * @param array $options
	 * @return self
	 */
	public function setOptions(array $options = [], $reset = false) {
		return $this->setFormat(current($options));
	}

	/**
	 * Filtracja dat
	 * @param mixed $value wartość
	 * @throws \Mmi\App\KernelException jeśli filtrowanie $value nie jest możliwe
	 * @return mixed
	 */
	public function filter($value) {
		//domyślny format jeśli brak
		!$this->getFormat() ? $this->setFormat('d.m.Y H:i:s') : null;
		$timestamp = $value;
		//nienumeryczna
		if (!is_numeric($value)) {
			$timestamp = strtotime($value);
		}
		return date($this->getFormat(), $timestamp);
	}

}

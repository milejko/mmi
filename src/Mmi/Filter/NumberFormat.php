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
 * Formater numerów
 */
class NumberFormat extends \Mmi\Filter\FilterAbstract {

	/**
	 * Filtruje zmienne numeryczne
	 * @param mixed $value wartość
	 * @throws \Mmi\App\KernelException jeśli filtrowanie $value nie jest możliwe
	 * @return mixed
	 */
	public function filter($value) {
		$digits = isset($this->_options[0]) ? $this->_options[0] : 2;
		$separator = isset($this->_options[1]) ? $this->_options[1] : ',';
		$thousands = isset($this->_options[2]) ? $this->_options[2] : ' ';
		$trimZeros = isset($this->_options[3]) ? $this->_options[3] : false;
		$trimLeaveZeros = isset($this->_options[4]) ? $this->_options[4] : 2;
		$value = number_format($value, $digits, $separator, $thousands);
		if ($trimZeros && strpos($value, $separator)) {
			$tmp = rtrim($value, '0');
			for ($i = 0, $missing = $trimLeaveZeros - ($digits - (strlen($value) - strlen($tmp))); $i < $missing; $i++) {
				$tmp .= '0';
			}
			$value = rtrim($tmp, '.,');
		}
		return str_replace('-', '- ', $value);
	}

}

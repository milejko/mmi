<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Validate;

class NumberBetween extends ValidateAbstract {

	/**
	 * Treść błędu 
	 */
	const INVALID = 'Wprowadzona wartość nie mieści się w wymaganym przedziale';

	/**
	 * Walidacja liczb od-do
	 * @param mixed $value wartość
	 * @return boolean
	 */
	public function isValid($value) {
		$from = isset($this->_options[0]) ? $this->_options[0] : 0;
		$to = isset($this->_options[1]) ? $this->_options[1] : 1000000000;
		if (($value < $from) || ($value > $to)) {
			$this->_error(self::INVALID);
			return false;
		}
		return true;
	}

}

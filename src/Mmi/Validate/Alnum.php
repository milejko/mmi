<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Validate;

class Alnum extends ValidateAbstract {

	/**
	 * Treść wiadomości
	 */
	const INVALID = 'Ciąg zawiera znaki inne niż litery i cyfry';

	/**
	 * Walidacja znaków alfanumerycznych
	 * @param mixed $value wartość
	 * @return boolean
	 */
	public function isValid($value) {

		if (!is_string($value) && !is_int($value) && !is_float($value)) {
			$this->_error(self::INVALID);
			return false;
		}

		$filter = new \Mmi\Filter\Alnum();

		if ($filter->filter($value) != $value) {
			$this->_error(self::INVALID);
			return false;
		}
		return true;
	}

}

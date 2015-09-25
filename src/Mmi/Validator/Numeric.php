<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Validator;

class Numeric extends ValidatorAbstract {

	/**
	 * Treść wiadomości
	 */
	const INVALID = 'Wprowadzona wartość nie jest liczbą';

	/**
	 * Walidacja liczb
	 * @param mixed $value wartość
	 * @return boolean
	 */
	public function isValid($value) {
		if (!is_numeric($value)) {
			$this->_error(self::INVALID);
			return false;
		}
		return true;
	}

}

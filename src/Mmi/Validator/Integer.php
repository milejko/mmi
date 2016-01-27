<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Validator;

/**
 * Walidator liczb całkowitych
 */
class Integer extends ValidatorAbstract {

	/**
	 * Treść wiadomości
	 */
	const INVALID = 'Wprowadzona wartość nie jest liczbą całkowitą';

	/**
	 * Walidacja liczb całkowitych
	 * @param mixed $value wartość
	 * @return boolean
	 */
	public function isValid($value) {
		//wartość nienumeryczna
		if (!is_numeric($value)) {
			return $this->_error(self::INVALID);
		}
		//wartość nie równa
		if (!(intval($value) == $value)) {
			return $this->_error(self::INVALID);
		}
		return true;
	}

}

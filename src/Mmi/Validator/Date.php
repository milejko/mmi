<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Validator;

class Date extends ValidatorAbstract {

	/**
	 * Treść wiadomości
	 */
	const INVALID = 'Wprowadzona wartość nie jest poprawną datą';

	/**
	 * Walidacja daty
	 * @param mixed $value wartość
	 * @return boolean
	 */
	public function isValid($value) {
		if (!strtotime($value)) {
			$this->_error(self::INVALID);
			return false;
		}
		return true;
	}

}

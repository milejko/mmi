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
 * Waliduje ciąg literowo cyfrowy
 */
class Alnum extends ValidatorAbstract {

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

		//nieprawidłowy typ danych
		if (!is_string($value) && !is_int($value) && !is_float($value)) {
			return $this->_error(self::INVALID);
		}
		//wartość filtrowana alnumem jest równa zadanej
		if ((new \Mmi\Filter\Alnum())->filter($value) == $value) {
			return true;
		}
		//w pozostałych przypadkach - błąd
		return $this->_error(self::INVALID);
	}

}

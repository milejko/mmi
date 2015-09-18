<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Validate;

class Postal extends ValidateAbstract {

	/**
	 * Komunikat błędnego kodu
	 */
	const ERROR = 'Wprowadzono niepoprawny kod pocztowy';

	/**
	 * Sprawdza czy tekst jest e-mailem
	 * @param string $value
	 * @return boolean
	 */
	public function isValid($value) {
		if (preg_match('/^[0-9]{2}-[0-9]{3}$/', $value)) {
			return true;
		}
		$this->_error(self::ERROR);
		return false;
	}

}

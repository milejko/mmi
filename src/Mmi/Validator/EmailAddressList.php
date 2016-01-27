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
 * Walidacja listy adresów
 */
class EmailAddressList extends ValidatorAbstract {

	/**
	 * Komunikat niedostatecznej długości
	 */
	const INVALID = 'Niepoprawny adres e-mail lub składnia';

	/**
	 * Sprawdza czy tekst jest e-mailem
	 * @param string $value
	 * @return boolean
	 */
	public function isValid($value) {
		$emails = explode(((false !== strpos($value = urldecode($value), ',')) ? ',' : ';'), $value);
		//iteracja po mailach
		foreach ($emails as $email) {
			//niepoprawny email
			if (!(new EmailAddress)->isValid($email)) {
				return $this->_error(self::INVALID);
			}
		}
		return true;
	}

}

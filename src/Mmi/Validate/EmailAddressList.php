<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Validate;

class EmailAddressList extends ValidateAbstract {

	/**
	 * Komunikat niedostatecznej długości
	 */
	const ERROR = 'Niepoprawny adres e-mail lub składnia';

	/**
	 * Sprawdza czy tekst jest e-mailem
	 * @param string $value
	 * @return boolean
	 */
	public function isValid($value) {
		if (empty($this->_options)) {
			$this->_options[] = ';';
		}
		$emails = explode($this->_options[0], urldecode($value));
		foreach ($emails as $email) {
			if (preg_match('/^([*+!.&#$¦\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i', trim($email))) {
				continue;
			}
			$this->_error(self::ERROR);
			return false;
		}
		return true;
	}

}

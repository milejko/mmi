<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Validate;

class Integer extends ValidateAbstract {

	/**
	 * Treść wiadomości
	 */
	const INVALID = 'Wprowadzona wartość nie jest liczbą całkowitą';

	/**
	 * Treść błędu o liczbie dodatniej
	 */
	const INVALID_POSITIVE = 'Wprowadzona wartość nie jest liczbą dodatnią';

	/**
	 * Walidacja liczb całkowitych
	 * @param mixed $value wartość
	 * @return boolean
	 */
	public function isValid($value) {
		$positive = (isset($this->_options['positive']) && $this->_options['positive']) ? true : false;
		if (!is_numeric($value)) {
			$this->_error(self::INVALID);
			return false;
		}
		if (!(intval($value) == $value)) {
			$this->_error(self::INVALID);
			return false;
		}
		if ($positive && !($value >= 0)) {
			$this->_error(self::INVALID_POSITIVE);
			return false;
		}
		return true;
	}

}

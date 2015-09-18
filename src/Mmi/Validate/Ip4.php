<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Validate;

class Ip4 extends ValidateAbstract {

	/**
	 * Treść wiadomości
	 */
	const INVALID = 'Niepoprawny adres IP';

	/**
	 * Walidacja IPv4
	 * @param mixed $value wartość
	 * @return boolean
	 */
	public function isValid($value) {
		if (!preg_match('/^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$/', $value)) {
			$this->_error(self::INVALID);
			return false;
		}
		foreach (explode('.', $value) as $num) {
			if ($num > 255 || $num < 0) {
				return false;
			}
		}
		return true;
	}

}

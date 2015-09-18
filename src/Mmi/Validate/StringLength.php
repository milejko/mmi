<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Validate;

class StringLength extends ValidateAbstract {

	/**
	 * Komunikat niedostatecznej długości
	 */
	const SHORT = 'Tekst jest zbyt krótki';

	/**
	 * Komunikat nadmiernej długości
	 */
	const LONG = 'Tekst jest zbyt długi';

	/**
	 * Waliduje długość ciągu, długość zadana jest w opcjach (przy konstruktorze)
	 * w tabeli postaci array(minimum, maksimum)
	 * @param string $value
	 * @return boolean
	 */
	public function isValid($value) {
		$short = isset($this->_options[0]) ? $this->_options[0] : 0;
		$long = isset($this->_options[1]) ? $this->_options[1] : 255;
		if (strlen($value) < $short) {
			$this->_error(self::SHORT);
			return false;
		}
		if (strlen($value) > $long) {
			$this->_error(self::LONG);
			return false;
		}
		return true;
	}

}

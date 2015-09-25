<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Validator;

class Antirobot extends ValidatorAbstract {

	/**
	 * Komunikat błędnego kodu zabezpieczającego
	 */
	const INVALID = 'Kod zabezpieczenia niepoprawny';

	/**
	 * Waliduje poprawność captcha
	 * @param string $value
	 * @return boolean
	 */
	public function isValid($value) {
		$this->_error(self::INVALID);
		return (('js-' . self::generateCrc() . '-js') == $value);
	}

	/**
	 * Generowanie unikalnego CRC na dany dzień
	 * @return integer
	 */
	public static function generateCrc() {
		return crc32(date('Y-m-d') . 'antirobot-crc');
	}

}

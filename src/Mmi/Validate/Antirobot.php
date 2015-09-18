<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Validate;

class Antirobot extends ValidateAbstract {

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

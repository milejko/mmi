<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Validator;

class Iban extends ValidatorAbstract {

	/**
	 * Treść błędu
	 */
	const INVALID = 'Wprowadzona wartość nie jest poprawnym numerem IBAN';

	/**
	 * Treść błędu o złym kraju IBAN
	 */
	const INVALID_COUNTRY = 'IBAN pochodzi z niedozwolonego kraju';

	/**
	 * Walidacja IBAN (rachunek bankowy)
	 * @param mixed $value wartość
	 * @return boolean
	 */
	public function isValid($value) {
		//kod kraju
		$country = isset($this->_options[0]) ? $this->_options[0] : 'PL';
		//znaki do usuniącia
		$trims = [' ', '-', '_', '.', ',', '/', '|'];
		//wielkie litery
		$tmp = strtoupper(str_replace($trims, '', $value));
		//brak pierwszego znaku
		if (!isset($tmp[0])) {
			$this->_error(self::INVALID);
			return false;
		}
		//brak kodu kraju - doklejanie
		if (is_numeric($tmp[0])) {
			$tmp = $country . $tmp;
		}
		//algorytm sumy kontrolnej
		$tmp = substr($tmp, 4) . substr($tmp, 0, 4);
		$tmp = str_replace([
			'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O',
			'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
		], [
			'10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22',
			'23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33', '34', '35'
		], $tmp);
		//błąd sumy kontrolnej
		if (bcmod($tmp, 97) != 1) {
			$this->_error(self::INVALID);
			return false;
		}
		return true;
	}

}

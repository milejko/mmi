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
 * Walidator json
 */
class Json extends ValidatorAbstract {

	/**
	 * Treść wiadomości
	 */
	const INVALID = 'JSON jest niepoprawny';

	/**
	 * Walidacja jsona
	 * @param mixed $value wartość
	 * @return boolean
	 */
	public function isValid($value) {
		try {
			\json_decode($value, true);
		} catch (\Exception $e) {
			return false;
		}
		return true;
	}

}

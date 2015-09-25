<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Validator;

class Regex extends ValidatorAbstract {

	/**
	 * Treść wiadomości
	 */
	const INVALID = 'Nieprawidłowy typ danych wejściowych';

	/**
	 * Komunikat - nie pasuje do wzorca
	 */
	const NOT_MATCH = 'Wartość nie pasuje do wzorca';

	/**
	 * Komunikat o błędzie wyrażenia regularnego
	 */
	const ERROROUS = 'Błędne wyrażenie regularne';

	/**
	 * Walidacja za pomocą wyrażenia regularnego
	 * @param mixed $value wartość
	 * @return boolean
	 */
	public function isValid($value) {
		$pattern = isset($this->_options[0]) ? $this->_options[0] : null;
		//jeśli nie podano wzorca, to przyjmujemy, że pasuje
		if (is_null($pattern)) {
			return true;
		}
		if (!is_string($value) && !is_int($value) && !is_float($value)) {
			$this->_error(self::INVALID);
			return false;
		}
		$status = preg_match($pattern, $value);
		if ($status === false) {
			$this->_error(self::ERROROUS);
			return false;
		}
		if (!$status) {
			$this->_error(self::NOT_MATCH);
			return false;
		}
		return true;
	}

}

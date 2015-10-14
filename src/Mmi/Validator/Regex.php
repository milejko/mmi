<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Validator;

/**
 * Walidator równości
 * 
 * @method self setPattern($pattern) ustawia pattern
 * @method self setMessage($message) ustawia własną wiadomość walidatora
 * 
 * @method string getPattern() pobiera pattern
 * @method string getMessage() pobiera wiadomość
 */
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
	 * Ustawia opcje
	 * @param array $options
	 * @return self
	 */
	public function setOptions(array $options = [], $reset = false) {
		return $this->setPattern(current($options))
				->setMessage(next($options));
	}

	/**
	 * Walidacja za pomocą wyrażenia regularnego
	 * @param mixed $value wartość
	 * @return boolean
	 */
	public function isValid($value) {
		//jeśli nie podano wzorca, to przyjmujemy, że pasuje
		if (is_null($this->getPattern()) || false === $this->getPattern()) {
			return true;
		}
		//błędny typ danych
		if (!is_string($value) && !is_int($value) && !is_float($value)) {
			return $this->_error(self::INVALID);
		}
		//błędne dopasowanie
		if (false === $status = preg_match($this->getPattern(), $value)) {
			return $this->_error(self::ERROROUS);
		}
		//ciąg nieodnaleziony
		if (!$status) {
			return $this->_error(self::NOT_MATCH);
		}
		return true;
	}

}

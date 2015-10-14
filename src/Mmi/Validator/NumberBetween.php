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
 * Walidator liczba pomiędzy
 * 
 * @method self setFrom($from) ustawia od
 * @method self setTo($to) ustawia do
 * @method self setMessage($message) ustawia własną wiadomość walidatora
 * 
 * @method integer getFrom() pobiera od
 * @method integer getTo() pobiera do
 * @method string getMessage() pobiera wiadomość 
 */
class NumberBetween extends ValidatorAbstract {

	/**
	 * Treść błędu 
	 */
	const INVALID = 'Wprowadzona wartość nie mieści się w wymaganym przedziale';

	/**
	 * Ustawia opcje
	 * @param array $options
	 * @return self
	 */
	public function setOptions(array $options = [], $reset = false) {
		return $this->setFrom(current($options))
				->setTo(next($options))
				->setMessage(next($options));
	}

	/**
	 * Walidacja liczb od-do
	 * @param mixed $value wartość
	 * @return boolean
	 */
	public function isValid($value) {
		//błąd
		if (($value < $this->getFrom()) || ($value > $this->getTo())) {
			return $this->_error(self::INVALID);
		}
		return true;
	}

}

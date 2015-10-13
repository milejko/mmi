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
 * Walidator długość ciągu pomiędzy
 * 
 * @method self setFrom($from) ustawia od
 * @method integer getFrom() pobiera od
 * @method self setTo($to) ustawia do
 * @method integer getTo() pobiera do
 */
class StringLength extends ValidatorAbstract {

	/**
	 * Komunikat niedostatecznej długości
	 */
	const SHORT = 'Tekst jest zbyt krótki';

	/**
	 * Komunikat nadmiernej długości
	 */
	const LONG = 'Tekst jest zbyt długi';

	/**
	 * Konstruktor tworzy opcje
	 * @param array $options
	 */
	public function __construct(array $options) {
		$this->setFrom(current($options))
			->setTo(next($options));
	}

	/**
	 * Waliduje długość ciągu, długość zadana jest w opcjach (przy konstruktorze)
	 * w tabeli postaci array(minimum, maksimum)
	 * @param string $value
	 * @return boolean
	 */
	public function isValid($value) {
		$short = $this->getFrom() ? $this->getFrom() : 0;
		$long = $this->getTo() ? $this->getTo() : 255;
		//za krótki
		if (mb_strlen($value) < $short) {
			return $this->_error(self::SHORT);
		}
		//za długi
		if (mb_strlen($value) > $long) {
			return $this->_error(self::LONG);
		}
		return true;
	}

}

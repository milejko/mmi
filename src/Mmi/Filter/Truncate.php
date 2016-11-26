<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Filter;

/**
 * Przycięcie ciągu znaków
 * 
 * @method self setLength($length)
 * @method getLength()
 * @method self setEnding($ending) końce linii
 * @method string getEnding()
 * @method self setBoundary($boundary) czy kończyć na pełnym wyrazie
 * @method boolean getBoundary()
 */
class Truncate extends \Mmi\Filter\FilterAbstract {

	/**
	 * Ustawia opcje
	 * @param array $options
	 * @return self
	 */
	public function setOptions(array $options = [], $reset = false) {
		return $this->setLength(current($options) ? (int) current($options) : 80)
			->setEnding(next($options) !== false ? current($options) : '...')
			->setBoundary(next($options) !== false ? true : false);
	}

	/**
	 * Obcina ciąg do zadanej długości
	 * @param mixed $value wartość
	 * @throws \Mmi\App\KernelException jeśli filtrowanie $value nie jest możliwe
	 * @return mixed
	 */
	public function filter($value) {
		$length = $this->getLength();
		//dostateczna długość
		if (mb_strlen($value, mb_detect_encoding($value)) < $length) {
			return $value;
		}
		$encoding = mb_detect_encoding($value);
		if ($this->getBoundary()) {
			$value = mb_substr($value, 0, $length, $encoding) . $this->getEnding();
		} else {
			$value = mb_substr($value, 0, $length, $encoding);
			if (strrpos($value, ' ') !== false) {
				$value = mb_substr($value, 0, strrpos($value, ' '), $encoding);
			}
			$value .= $this->getEnding();
		}

		return $value;
	}

}

<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Filter;

/**
 * Formater numerów
 * 
 * @method self setDigits($digits) ilość znaków
 * @method integer getDigits() pobiera ilość znaków
 * @method self setSeparator($separator) znak separatora przecinka
 * @method string getSeparator()
 * @method self setThousands($thousands) separator tysięcy
 * @method string getThousands()
 * @method self setTrimZeros($trim) czy ucina zera
 * @method boolean getTrimZeros()
 * @method self setTrimLeaveZeros($leave) ilość zer po przecinku
 * @method integer getTrimLeaveZeros()
 */
class NumberFormat extends \Mmi\Filter\FilterAbstract {

	/**
	 * Ustawia opcje
	 * @param array $options
	 * @return self
	 */
	public function setOptions(array $options = [], $reset = false) {
		return $this->setDigits(current($options) ? (int) current($options) : 2)
				->setSeparator(next($options) ? current($options) : ',')
				->setThousands(next($options) ? current($options) : ' ')
				->setTrimZeros(next($options) ? (bool) current($options) : false)
				->setTrimLeaveZeros(next($options) ? current($options) : 2);
	}

	/**
	 * Filtruje zmienne numeryczne
	 * @param mixed $value wartość
	 * @throws \Mmi\App\KernelException jeśli filtrowanie $value nie jest możliwe
	 * @return mixed
	 */
	public function filter($value) {
		$value = number_format($value, $this->getDigits(), $this->getSeparator(), $this->getThousands());
		if ($this->getTrimZeros() && strpos($value, $this->getSeparator())) {
			$tmp = rtrim($value, '0');
			//iteracja po brakujących zerach
			for ($i = 0, $missing = $this->getTrimLeaveZeros() - ($this->getDigits() - (strlen($value) - strlen($tmp))); $i < $missing; $i++) {
				$tmp .= '0';
			}
			$value = rtrim($tmp, '.,');
		}
		return str_replace('-', '- ', $value);
	}

}

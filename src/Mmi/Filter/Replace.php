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
 * Zamiana znaków
 * @method self setSearch($search)
 * @method string getSearch()
 * @method self setReplace($replace)
 * @method string getReplace()
 */
class Replace extends \Mmi\Filter\FilterAbstract {

	/**
	 * Ustawia opcje
	 * @param array $options
	 * @return self
	 */
	public function setOptions(array $options = [], $reset = false) {
		return $this->setSearch(current($options))
				->setReplace(next($options));
	}

	/**
	 * Zamienia znaki
	 * @param mixed $value wartość
	 * @throws \Mmi\App\KernelException jeśli filtrowanie $value nie jest możliwe
	 * @return mixed
	 */
	public function filter($value) {
		return str_replace($this->getSearch(), $this->getReplace(), $value);
	}

}

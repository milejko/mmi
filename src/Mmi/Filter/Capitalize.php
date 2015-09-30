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
 * Filtr powiększający litery
 */
class Capitalize extends \Mmi\Filter\FilterAbstract {

	/**
	 * Zwiększa wszystkie litery w każdym wyrazie ciągu
	 * @param mixed $value wartość
	 * @throws \Mmi\App\KernelException jeśli filtrowanie $value nie jest możliwe
	 * @return mixed
	 */
	public function filter($value) {
		return mb_convert_case($value, MB_CASE_TITLE, mb_detect_encoding($value));
	}

}

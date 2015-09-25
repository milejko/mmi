<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Filter;

class StripTags extends \Mmi\Filter\FilterAbstract {

	/**
	 * Kasuje html'a
	 * @param mixed $value wartość
	 * @throws \Mmi\App\Exception jeśli filtrowanie $value nie jest możliwe
	 * @return mixed
	 */
	public function filter($value) {
		$except = '';
		if (isset($this->_options['exceptions'])) {
			$except = $this->_options['exceptions'];
		}
		return strip_tags($value, $except);
	}

}

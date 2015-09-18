<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Filter;

class IntToNull extends \Mmi\Filter\FilterAbstract {

	/**
	 * Zamiana 1 na true i 0 na false
	 * @param mixed $value wartość
	 * @return boolean
	 */
	public function filter($value) {
		return ($value == 0) ? false : true;
	}

}

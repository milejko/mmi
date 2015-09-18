<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Filter;

class MarkupProperty extends \Mmi\Filter\FilterAbstract {

	/**
	 * Zmienia zmienną, tak by mogła być wykorzystana wewnątrz właściwości znacznika HTML
	 * @param mixed $value wartość
	 * @throws Exception jeśli filtrowanie $value nie jest możliwe
	 * @return mixed
	 */
	public function filter($value) {
		$value = trim(strip_tags($value));

		$value = str_replace([
			'\'',
			'`',
			',',
			'"',
			'#',
			'?',
			'=',
		], [
			'',
			'',
			'',
			'',
			'',
			'',
			'',
		], $value);
		return $value;
	}

}

<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Validate;

class RecordUnique extends ValidateAbstract {

	/**
	 * Komunikat istnienia pola
	 */
	const EXISTS = 'Pole o takiej wartości już istnieje';

	/**
	 * Walidacja unikalności rekordu z użyciem Query
	 * @param mixed $value wartość
	 * @return boolean
	 */
	public function isValid($value) {
		if (!isset($this->_options[0]) || !($this->_options[0] instanceof \Mmi\Orm\Query)) {
			throw new \Exception('No query class supplied.');
		}
		if (!isset($this->_options[1])) {
			throw new \Exception('No field name supplied.');
		}
		$q = $this->_options[0];
		/* @var $q \Mmi\Orm\Query */
		$q->where($this->_options[1])->equals($value);
		if (isset($this->_options[2])) {
			$q->andField('id')->notEquals(intval($this->_options[2]));
		}
		if ($q->count() > 0) {
			$this->_error(self::EXISTS);
			return false;
		}
		return true;
	}

}

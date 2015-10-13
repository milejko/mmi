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
 * Walidator unikalności rekordu
 * 
 * @method self setQuery(\Mmi\Orm\Query $query) ustawia querę
 * @method \Mmi\Orm\Query getQuery() pobiera querę
 * @method self setField($field) ustawia nazwę pola
 * @method string getField() pobiera nazwę pola
 * @method self setId($id) ustawia ID
 * @method integer getId() pobiera ID
 */
class RecordUnique extends ValidatorAbstract {

	/**
	 * Komunikat istnienia pola
	 */
	const EXISTS = 'Pole o takiej wartości już istnieje';

	/**
	 * Ustawia opcje
	 * @param array $options
	 * @return self
	 */
	public function setOptions(array $options = [], $reset = false) {
		return $this->setQuery(current($options))
			->setField(next($options))
			->setId(next($options));
	}

	/**
	 * Walidacja unikalności rekordu z użyciem Query
	 * @param mixed $value wartość
	 * @return boolean
	 */
	public function isValid($value) {
		//niepoprawna quera
		if (!($this->getQuery() instanceof \Mmi\Orm\Query)) {
			throw new ValidatorException('No query class supplied.');
		}
		//brak pola
		if (!$this->getField()) {
			throw new ValidatorException('No field name supplied.');
		}
		$q = $this->getQuery();
		/* @var $q \Mmi\Orm\Query */
		$q->where($this->getField())->equals($value);
		if ($this->getId()) {
			$q->andField('id')->notEquals(intval($this->getId()));
		}
		//rekord istnieje
		if ($q->count() > 0) {
			return $this->_error(self::EXISTS);
		}
		return true;
	}

}

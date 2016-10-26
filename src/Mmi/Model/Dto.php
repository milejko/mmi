<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Model;

/**
 * Klasa obiektów transferu
 */
abstract class Dto {

	protected $_replacementFields = [];
	protected $_readOnlyFields = ['id'];

	/**
	 * Konstruktor buduje DTO na podstawie tablicy lub obiektu
	 * @param mixed $data
	 */
	public function __construct($data = null) {
		//tablica
		if (is_array($data)) {
			return $this->setFromArray($data);
		}
		//rekord ORM
		if ($data instanceof \Mmi\Orm\Record) {
			return $this->setFromOrmRecord($data);
		}
		//dto
		if ($data instanceof \Mmi\Model\Dto) {
			return $this->setFromArray($data->toArray());
		}
		//stdClass
		if ($data instanceof \stdClass) {
			return $this->setFromArray((array)$data);
		}
		//brak danych
		if ($data === null) {
			return;
		}
		//pozostałe typy danych są niewspierane
		throw new DtoException('Invalid input data: ' . (gettype($data) === 'object' ? get_class($data) : gettype($data)) . ' is not supported');
	}

	/**
	 * Ustawia obiekt DTO na podstawie tabeli
	 * @param array $data
	 * @return \Mmi\Model\Api\Dto
	 */
	public final function setFromArray(array $data) {
		//iteracja po danych
		foreach ($data as $key => $value) {
			//własność nie istnieje
			if (!property_exists($this, $key)) {
				continue;
			}
			$this->{$key} = is_string($value) ? trim($value) : $value;
		}
		//iteracja po zamiennikach
		foreach ($this->_replacementFields as $recordKey => $dtoKey) {
			if (!is_array($dtoKey)) {
				$dtoKey = [$dtoKey];
			}
			//obsługa wielu zastąpień z jednego klucza rekordu
			foreach ($dtoKey as $dKey) {
				//własność nie istnieje
				if (!property_exists($this, $dKey)) {
					continue;
				}
				if (!array_key_exists($recordKey, $data)) {
					continue;
				}
				$this->$dKey = trim($data[$recordKey]);
			}
		}
		return $this;
	}

	/**
	 * Ustawia obiekt z \Mmi\Orm\Record
	 * @param \Mmi\Orm\Record $record
	 * @return \Mmi\Model\Api\Dto
	 */
	public final function setFromOrmRecord(\Mmi\Orm\Record $record) {
		return $this->setFromArray($record->toArray());
	}

	/**
	 * Konwertuje DTO do tabeli (dane wyjściowe)
	 * @return array
	 */
	public final function toArray() {
		$data = [];
		//iteracja po danych
		foreach ($this as $key => $value) {
			$data[$key] = $value;
		}
		return $data;
	}

	/**
	 * Konwertuje DTO do tabeli (dane wejściowe)
	 * @return array
	 */
	public final function toArrayPut() {
		$data = [];
		//iteracja po danych
		foreach ($this as $key => $value) {
			//znaleziono w read only
			if (false !== in_array($key, $this->_readOnlyFields)) {
				continue;
			}
			//znaleziono w read only po zamianach
			if (false !== ($replaceKey = array_search($key, $this->_replacementFields))) {
				$key = $replaceKey;
			}
			$data[$key] = $value;
		}
		return $data;
	}

}

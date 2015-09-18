<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Orm\Record;

class Collection extends \ArrayObject {

	/**
	 * Kasuje całą kolekcję obiektów
	 * @return integer ilość usuniętych obiektów
	 */
	public function delete() {
		$i = 0;
		foreach ($this as $ar) {
			$ar->delete();
			$i++;
		}
		return $i;
	}

	/**
	 * Zwraca kolekcję w postaci tablicy
	 * @return array
	 */
	public function toArray() {
		$array = [];
		foreach ($this as $key => $record) {
			$array[$key] = $record->toArray();
		}
		return $array;
	}

	/**
	 * Zwraca kolekcję w postaci tablicy obiektów
	 * @return \Mmi\Orm\Record\Collection
	 */
	public function toObjectArray() {
		$array = [];
		foreach ($this as $key => $record) {
			$array[$key] = $record;
		}
		return $array;
	}

	/**
	 * Zwraca kolekcję w postaci JSON
	 * @return string
	 */
	public function toJson() {
		return json_encode($this->toArray());
	}

	/**
	 * Klonuje całą kolekcję
	 */
	public function __clone() {
		foreach ($this as $key => $record) {
			$this[$key] = clone $record;
		}
	}

}

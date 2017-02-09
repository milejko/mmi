<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Cache;

/**
 * Handler bufora w APC
 */
class ApcHandler extends DistributedCacheHandlerAbstract {

	/**
	 * Ładuje dane o podanym kluczu
	 * @param string $key klucz
	 */
	public function load($key) {
		//czy klucz powinien zostać usunięty
		if ($this->_keyShouldBeDeleted($key)) {
			//nie usuwamy z bufora APC (strata wydajności)
			return;
		}
		//rozgłoszenie informacji o usunięciu klucza do bufora Db
		return \apcu_fetch($this->_namespace . $key);
	}

	/**
	 * Zapisuje dane pod podanym kluczem
	 * @param string $key klucz
	 * @param string $data
	 * @param int $lifeTime wygaśnięcie danych w buforze (informacja dla bufora)
	 * @param boolean
	 */
	public function save($key, $data, $lifeTime) {
		//zapis w APC
		\apcu_store($this->_namespace . $key, $data, $lifeTime);
		return true;
	}

	/**
	 * Kasuje dane o podanym kluczu
	 * @param string $key klucz
	 * @return boolean
	 */
	public function delete($key) {
		//rozgłoszenie informacji o usunięciu klucza do bufora Db
		$this->_markToDelete($key);
		//usunięcie z apc
		\apcu_delete($this->_namespace . $key);
		return true;
	}

	/**
	 * Kasuje wszystkie dane
	 */
	public function deleteAll() {
		//czyszczenie bufora apc
		return \apcu_clear_cache();
	}

}

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
 * Plikowy backend bufora
 */
class FileHandler extends DistributedCacheHandlerAbstract {

	/**
	 * Ładuje dane o podanym kluczu
	 * @param string $key klucz
	 */
	public function load($key) {
		//czy klucz powinien zostać usunięty
		if ($this->_keyShouldBeDeleted($key)) {
			//nie usuwamy pliku (strata wydajności)
			return;
		}
		//plik istnieje
		if (file_exists($this->_cache->getConfig()->path . '/' . $key)) {
			//odczyt pliku
			return file_get_contents($this->_cache->getConfig()->path . '/' . $key);
		}
	}

	/**
	 * Zapisuje dane pod podanym kluczem
	 * @param string $key klucz
	 * @param string $data
	 * @param int $lifeTime wygaśnięcie danych w buforze (informacja dla bufora)
	 */
	public function save($key, $data, $lifeTime) {
		//zapis pliku
		if (file_put_contents($this->_cache->getConfig()->path . '/' . $key, $data) === false) {
			return false;
		}
		return true;
	}

	/**
	 * Kasuje dane o podanym kluczu
	 * @param string $key klucz
	 */
	public function delete($key) {
		//rozgłoszenie informacji o usunięciu klucza do bufora Db
		$this->_markToDelete($key);
		//jeśli plik istnieje
		if (file_exists($this->_cache->getConfig()->path . '/' . $key)) {
			//usuwanie pliku
			unlink($this->_cache->getConfig()->path . '/' . $key);
		}
		return true;
	}

	/**
	 * Kasuje wszystkie dane
	 */
	public function deleteAll() {
		//iteracja po plikach
		foreach (glob($this->_cache->getConfig()->path . '/*') as $fileName) {
			//usuwanie pliku
			unlink($fileName);
		}
	}

}

<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Cache;

use \Mmi\App\KernelException;

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
		//próba odczytu pliku
		try {
			return file_get_contents($this->_cache->getConfig()->path . '/' . $key);
		} catch (KernelException $e) {
			//brak akcji
		}
	}

	/**
	 * Zapisuje dane pod podanym kluczem
	 * @param string $key klucz
	 * @param string $data
	 * @param int $lifeTime wygaśnięcie danych w buforze (informacja dla bufora)
	 */
	public function save($key, $data, $lifeTime) {
		//$lifeTime nie jest używany w tym backendzie
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
		//próba usunięcia pliku
		try {
			unlink($this->_cache->getConfig()->path . '/' . $key);
		} catch (KernelException $e) {
			//brak akcji
		}
		//rozgłoszenie informacji o usunięciu klucza do bufora Db
		$this->_broadcastDelete($key);
		return true;
	}

	/**
	 * Kasuje bufor bez rozgłaszania
	 */
	protected function _deleteAllNoBroadcasting() {
		//iteracja po plikach
		foreach (glob($this->_cache->getConfig()->path . '/*') as $filename) {
			//bez usuwania katalogu
			if (is_dir($filename)) {
				continue;
			}
			//usuwanie pliku
			unlink($filename);
		}
	}

}

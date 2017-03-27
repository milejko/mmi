<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Cache;

use Mmi\Orm\CacheQuery,
	Mmi\Orm\CacheRecord;

/**
 * Rozproszony storage (oparty o DB)
 */
class DistributedStorage extends \Mmi\OptionObject {

	/**
	 * 1/x prawdopodobieństwo uruchomienia garbage collectora
	 */
	CONST GARBAGE_COLLECTOR_DIVISOR = 500;

	/**
	 * Maksymalny czas rozgłaszania
	 */
	CONST DEFAULT_TTL = 300;

	/**
	 * Kostruktor
	 */
	public function __construct() {
		//garbage collector
		if (rand(1, self::GARBAGE_COLLECTOR_DIVISOR) == 1) {
			//uproszczone usuwanie - jedynm zapytaniem
			\Mmi\Orm\DbConnector::getAdapter()->delete((new CacheQuery)->getTableName());
		}
		//iteracja po kolekcji aktywnego bufora systemowego
		foreach ((new CacheQuery)
			->whereTtl()->greater(time())
			->find() as $cacheRecord) {
			//próba rozkodowania danych
			try {
				//zapis danych do rejestru
				$this->setOption($cacheRecord->id, $cacheRecord->data);
			} catch (\Exception $e) {
				//błąd json
			}
		}
	}

	/**
	 * Zapisuje dane pod podanym kluczem
	 * @param string $data
	 * @param string $key klucz
	 */
	public function save($data, $key) {
		//wyszukiwanie rekordu
		if (null === $cacheRecord = (new CacheQuery)->findPk($key)) {
			//tworzenie nowego rekordu
			$cacheRecord = new CacheRecord;
			$cacheRecord->id = $key;
		}
		$cacheRecord->data = $data;
		$cacheRecord->ttl = time() + self::DEFAULT_TTL;
		//aktualizacja w rejestrze
		$this->setOption($key, $data);
		//próba zapisu
		try {
			//zapis rekordu
			$cacheRecord->save();
		} catch (\Exception $e) {
			//slam?
		}
	}

}

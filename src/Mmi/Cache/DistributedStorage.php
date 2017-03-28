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
	CONST GARBAGE_COLLECTOR_DIVISOR = 1000;

	/**
	 * Maksymalny czas rozgłaszania
	 * czas w którym musi odezwać się każdy node
	 */
	CONST DEFAULT_TTL = 60;
	
	/**
	 * Kostruktor
	 */
	public function __construct() {
		//garbage collector
		if (rand(0, self::GARBAGE_COLLECTOR_DIVISOR) == 1) {
			//uproszczone usuwanie - jedynm zapytaniem
			\Mmi\Orm\DbConnector::getAdapter()->delete((new CacheQuery)->getTableName());
		}
		//iteracja po parach klucz+dane storage w mmi_cache
		foreach ((new CacheQuery)
			->whereTtl()->greater(time())
			->findPairs('id', 'data') as $id => $data) {
			//zapis danych do rejestru
			$this->setOption($id, $data);
		}
	}

	/**
	 * Zapisuje dane pod podanym kluczem
	 * @param string $data
	 * @param string $key klucz
	 */
	public function save($data, $key) {
		//tworzenie nowego rekordu
		$cacheRecord = new CacheRecord;
		$cacheRecord->clearModified();
		//nadawanie identyfikatora
		$cacheRecord->id = $key;
		//ustawianie danych
		$cacheRecord->data = $data;
		//ustawienie ttl
		$cacheRecord->ttl = time() + self::DEFAULT_TTL;
		//aktualizacja w rejestrze
		$this->setOption($key, $data);
		return $cacheRecord->save();
	}
	
}

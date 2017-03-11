<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Cache;

use \Mmi\Orm;

/**
 * Bazodanowy handler bufora
 */
class DbHandler implements CacheHandlerInterface {

	/**
	 * Obiekt bufora
	 * @var \Mmi\Cache\Cache
	 */
	private $_cache;

	/**
	 * Prefiks kluczy systemowych
	 * @var string
	 */
	CONST SYSTEM_CACHE_PREFIX = 'mmi-';

	/**
	 * Prefiks kluczy systemowych
	 * @var string
	 */
	CONST SYSTEM_DATA_KEY = 'mmi-system-keys';

	/**
	 * Czas odświeżania bufora rozproszonego
	 */
	CONST REFRESH_TIME = 3;

	/**
	 * 1/x prawdopodobieństwo uruchomienia garbage collectora
	 */
	CONST GARBAGE_COLLECTOR_DIVISOR = 500;

	/**
	 * Kostruktor
	 * @param \Mmi\Cache\Cache $cache obiekt bufora
	 */
	public function __construct(\Mmi\Cache\Cache $cache) {
		//ustawienie obiektu bufora
		$this->_cache = $cache;
		//handler bazodanowy nie obsługuje trybu rozproszonego
		if ($cache->getConfig()->distributed) {
			throw new CacheException('DB handler doesn\'t allow distributed mode');
		}
		//wczytanie danych
		$this->_initializeSystemData($cache);
	}

	/**
	 * Inicjalizacja danych systemowych
	 * @param \Mmi\Cache\Cache $cache
	 */
	protected function _initializeSystemData(Cache $cache) {
		$tmpCache = $this->_getTmpCache();
		//wczytanie danych z bufora
		if (null === $systemEntries = $tmpCache->load(self::SYSTEM_DATA_KEY)) {
			//garbage collector
			$this->_gc();
			//zapis do tymczasowego bufora
			$tmpCache->save($systemEntries = (new Orm\CacheQuery)
				->whereTtl()->greater(time())
				->whereId()->like(self::SYSTEM_CACHE_PREFIX . '%')
				->find(), self::SYSTEM_DATA_KEY, self::REFRESH_TIME);
		}
		//iteracja po kolekcji aktywnego bufora systemowego
		foreach ($systemEntries as $cacheRecord) {
			//próba rozkodowania danych
			try {
				//walidacja i zapis danych do rejestru
				$cache->validateAndPrepareBackendData($cacheRecord->id, \json_decode($cacheRecord->data));
			} catch (\Exception $e) {
				//błąd json
			}
		}
	}

	/**
	 * Pobranie podręcznego bufora
	 * @return \Mmi\Cache\Cache
	 */
	protected function _getTmpCache() {
		//inicjalizacja tymczasowego bufora
		$tmpConfig = new CacheConfig;
		//jeśli dostępny jest apc - preferowany
		\extension_loaded('apcu') ? $tmpConfig->handler = 'apc' : $tmpConfig->path = sys_get_temp_dir();
		//zwrot obiektu bufora
		return new Cache($tmpConfig);
	}

	/**
	 * Garbage collector
	 */
	protected function _gc() {
		//garbage collector
		if (rand(1, self::GARBAGE_COLLECTOR_DIVISOR) == 1) {
			//uproszczone usuwanie - jednym zapytaniem
			\Mmi\Orm\DbConnector::getAdapter()->delete((new Orm\CacheQuery)->getTableName(), 'WHERE ttl < :ttl', ['ttl' => time()]);
		}
	}

	/**
	 * Ładuje dane o podanym kluczu
	 * @param string $key klucz
	 */
	public function load($key) {
		//zapytanie przepuszczone przez obiekt Cache
		//bufor systemowy był wczytany - brak w rejestrze
		if (substr($key, 0, strlen(self::SYSTEM_CACHE_PREFIX)) == self::SYSTEM_CACHE_PREFIX) {
			return;
		}
		//wyszukiwanie rekordu
		if (null === $cacheRecord = (new Orm\CacheQuery)->findPk($key)) {
			return;
		}
		//zwrot danych
		return \json_decode($cacheRecord->data);
	}

	/**
	 * Zapisuje dane pod podanym kluczem
	 * @param string $key klucz
	 * @param string $data
	 * @param int $lifeTime wygaśnięcie danych w buforze (informacja dla bufora)
	 */
	public function save($key, $data, $lifeTime) {
		//wyszukiwanie rekordu
		if (null === $cacheRecord = (new Orm\CacheQuery)->findPk($key)) {
			//tworzenie nowego rekordu
			$cacheRecord = new Orm\CacheRecord;
			$cacheRecord->id = $key;
		}
		$cacheRecord->data = json_encode($data);
		$cacheRecord->ttl = time() + $lifeTime + 1 + self::REFRESH_TIME;
		//próba zapisu
		try {
			//zapis rekordu
			$cacheRecord->save();
		} catch (\Exception $e) {
			//slam?
		}
		return true;
	}

	/**
	 * Kasuje dane o podanym kluczu
	 * @param string $key klucz
	 * @return boolean
	 */
	public function delete($key) {
		//wyszukiwanie rekordu
		if (null === $cacheRecord = (new Orm\CacheQuery)->findPk($key)) {
			return true;
		}
		//usuwanie rekordu
		return $cacheRecord->delete() || true;
	}

	/**
	 * Kasuje wszystkie dane
	 */
	public function deleteAll() {
		//uproszczone usuwanie - jedynm zapytaniem
		\Mmi\Orm\DbConnector::getAdapter()->delete((new Orm\CacheQuery)->getTableName());
	}

}

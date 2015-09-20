<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Orm;

/**
 * Klasa dostępu do tabel w bazie danych
 */
class DbConnector {

	/**
	 * Przechowuje strukturę bazy danych
	 * @var array
	 */
	protected static $_tableStructure = [];

	/**
	 * Adapter DB
	 * @var \Mmi\Db\Adapter\Pdo\PdoAbstract
	 */
	protected static $_adapter;

	/**
	 * Obiekt bufora
	 * @var \Mmi\Cache
	 */
	protected static $_cache;

	/**
	 * Zabezpieczony konstruktor
	 */
	private final function __construct() {
		
	}

	/**
	 * Pobiera adapter bazodanowy
	 * @return \Mmi\Db\Adapter\Pdo\PdoAbstract
	 */
	public static final function getAdapter() {
		//brak lub nieprawidłowy adapter
		if (!(static::$_adapter instanceof \Mmi\Db\Adapter\Pdo\PdoAbstract)) {
			throw new \Exception('\Mmi\Orm: Adapter not specified or invalid');
		}
		//zwrot adaptera
		return static::$_adapter;
	}

	/**
	 * Ustawia adapter bazodanowy
	 * @param \Mmi\Db\Adapter\Pdo\PdoAbstract $adapter
	 * @return \Mmi\Db\Adapter\Pdo\PdoAbstract
	 */
	public static final function setAdapter(\Mmi\Db\Adapter\Pdo\PdoAbstract $adapter) {
		return static::$_adapter = $adapter;
	}

	/**
	 * Zwraca obiekt cache
	 * @return \Mmi\Cache
	 */
	public static final function getCache() {
		return static::$_cache;
	}

	/**
	 * Ustawia obiekt cache
	 * @param \Mmi\Cache $cache
	 * @return \Mmi\Cache
	 */
	public static final function setCache(\Mmi\Cache $cache) {
		return static::$_cache = $cache;
	}

	/**
	 * Pobiera strukturę tabeli
	 * @param string $tableName opcjonalna nazwa tabeli
	 * @return array
	 */
	public static final function getTableStructure($tableName) {
		//pobranie struktury z obiektu (wcześniej zapisane)
		if (isset(self::$_tableStructure[$tableName])) {
			return self::$_tableStructure[$tableName];
		}
		//pobranie z cache
		$cacheKey = 'Orm-structure-' . self::getAdapter()->getConfig()->name . '-' . $tableName;
		if (static::$_cache !== null && (null !== ($structure = static::$_cache->load($cacheKey)))) {
			return $structure;
		}
		//pobranie z adaptera
		$structure = static::getAdapter()->tableInfo($tableName);
		if (static::$_cache !== null) {
			static::$_cache->save($structure, $cacheKey, 28800);
		}
		//zwrot i zapis do tablicy zapisanych struktur
		return self::$_tableStructure[$tableName] = $structure;
	}

	/**
	 * Resetuje struktury tabel i usuwa cache
	 * @return boolean
	 */
	public static final function resetTableStructures() {
		//usunięcie struktrur z cache
		foreach (self::getAdapter()->tableList() as $tableName) {
			static::$_cache->remove('Orm-structure-' . self::getAdapter()->getConfig()->name . '-' . $tableName);
		}
		//usunięcie lokalnie zapisanych struktur
		self::$_tableStructure = [];
		return true;
	}

	/**
	 * Zwraca obecność pola w tabeli
	 * @param string $fieldName nazwa pola
	 * @param string $tableName opcjonalna nazwa tabeli
	 * @return boolean
	 */
	public static final function fieldInTable($fieldName, $tableName) {
		return isset(self::getTableStructure($tableName)[$fieldName]);
	}

	/**
	 * Zwraca nazwę rekordu dla podanej tabeli
	 * @param string $tableName
	 * @return string
	 */
	public static final function getRecordNameByTable($tableName) {
		//rozdzielenie po podkreślniku
		$tableArray = explode('_', $tableName);
		$firstElement = $tableArray[0];
		array_shift($tableArray);
		array_unshift($tableArray, $firstElement, 'Orm');
		$tableArray[] = 'Record';
		//dołączenie pozostałych parametrów
		foreach ($tableArray as $key => $element) {
			$tableArray[$key] = ucfirst($element);
		}
		//łączenie z \
		return implode('\\', $tableArray);
	}

}

<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\App;

/**
 * Abstrakcyjna klasa rejestru aplikacji
 */
abstract class Registry {

	/**
	 * Konfiguracja aplikacji
	 * @var \App\KernelConfig
	 */
	public static $config;
	
	/**
	 * Obiekt bufora
	 * @var \Mmi\Cache\Cache
	 */
	public static $cache;

	/**
	 * Obiekt adaptera bazodanowego
	 * @var \Mmi\Db\Adapter\Pdo\PdoAbstract
	 */
	public static $db;

	/**
	 * Tablica ze zmiennymi użytkownika
	 * @var array
	 */
	protected static $_userData = [];

	/**
	 * Ustawia zmienną użytkownika
	 * @param string $key
	 * @param mixed $value
	 * @return mixed
	 */
	public static final function setVar($key, $value) {
		return static::$_userData[$key] = $value;
	}

	/**
	 * Kasuje zmienną użytkownika
	 * @param string $key
	 */
	public static final function unsetVar($key) {
		unset(static::$_userData[$key]);
	}

	/**
	 * Zwraca zmienną użytkownika
	 * @param string $key
	 * @return mixed
	 */
	public static final function getVar($key) {
		return isset(static::$_userData[$key]) ? static::$_userData[$key] : null;
	}

	/**
	 * Sprawdza istnienie zmiennej użytkownika
	 * @param string $key
	 * @return boolean
	 */
	public static final function issetVar($key) {
		return array_key_exists($key, static::$_userData);
	}

	/**
	 * Zwraca wszystkie zmienne użytkownika w tablicy
	 * @return array
	 */
	public static final function getAllVars() {
		return static::$_userData;
	}

}

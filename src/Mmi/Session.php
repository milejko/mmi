<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi;

class Session {

	/**
	 * Zwraca czy ustawiona jest przestrzeń nazw
	 * @param  string $name nazwa przestrzeni
	 * @return boolean
	 */
	public static function namespaceIsset($name) {
		return isset($_SESSION[$name]);
	}

	/**
	 * Rozpoczęcie sesji
	 * @param \Mmi\Session\Config $config
	 */
	public static function start(\Mmi\Session\Config $config) {
		session_name($config->name);
		session_set_cookie_params($config->cookieLifetime);
		session_cache_expire($config->cacheExpire);
		ini_set('session.gc_divisor', $config->gcDivisor);
		ini_set('session.gc_maxlifetime', $config->gcMaxLifetime);
		ini_set('session.gc_probability', $config->gcProbability);
		ini_set('session.save_handler', $config->handler);
		session_save_path($config->path);
		session_start();
	}

	/**
	 * Ustawia identyfikator sesji
	 * zwraca ustawiony identyfikator
	 * @param string $id identyfikator
	 * @return string
	 */
	public static function setId($id) {
		return session_id($id);
	}

	/**
	 * Pobiera identyfikator sesji
	 * @return string
	 */
	public static function getId() {
		return session_id();
	}

	/**
	 * Pobiera przekształcony do integera identyfikator sesji
	 * @return int
	 */
	public static function getNumericId() {
		$hashId = self::getId();
		$id = (integer) substr(preg_replace('/[a-z]+/', '', $hashId), 0, 9);
		$letters = preg_replace('/[0-9]+/', '', $hashId);
		for ($i = 0, $length = strlen($letters); $i < $length; $i++) {
			$id += ord($letters[$i]) - 97;
		}
		return $id;
	}

	/**
	 * Niszczy sesję
	 * @return boolean
	 */
	public static function destroy() {
		return session_destroy();
	}

	/**
	 * Regeneruje identyfikator sesji
	 * kopiuje dane starej sesji do nowej
	 * @param boolean $deleteOldSession kasuje starą sesję
	 * @return boolean
	 */
	public static function regenerateId($deleteOldSession = false) {
		return session_regenerate_id($deleteOldSession);
	}

}

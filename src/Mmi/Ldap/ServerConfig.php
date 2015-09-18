<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Ldap;

/**
 * Klasa konfiguracji klienta LDAP
 * 
 * @method ServerConfig getAddress()
 * @method ServerConfig setAddress($address) adres wraz z protokołem i portem np. ldap://example.com:389, można podać tablicę
 * @method ServerConfig getUser()
 * @method ServerConfig setUser($user)
 * @method ServerConfig getPassword()
 * @method ServerConfig setPassword($password)
 * @method ServerConfig getDomain()
 * @method ServerConfig setDomain($domain)
 */
class ServerConfig extends \Mmi\OptionObject {

	/**
	 * Fabryka obiektów
	 * @return ServerConfig
	 */
	public static function factory() {
		return new self();
	}

	/**
	 * Zabezpieczony konstruktor
	 */
	private function __construct() {
		
	}

}

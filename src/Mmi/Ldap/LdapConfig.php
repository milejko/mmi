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
 * @method mixed getAddress()
 * @method LdapConfig setAddress($address) adres wraz z protokołem i portem np. ldap://example.com:389, można podać tablicę
 * @method string getUser()
 * @method LdapConfig setUser($user)
 * @method string getPassword()
 * @method LdapConfig setPassword($password)
 * @method string getDomain()
 * @method LdapConfig setDomain($domain)
 */
class LdapConfig extends \Mmi\OptionObject {

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

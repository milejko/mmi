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
 */
class LdapConfig {

	/**
	 * Aktywny
	 * @var boolean
	 */
	public $active = false;

	/**
	 * Adres lub tablica adresów
	 * @var string|array
	 */
	public $address;

	/**
	 * Użytkownik
	 * @var string
	 */
	public $user;

	/**
	 * Hasło
	 * @var string
	 */
	public $password;

	/**
	 * Domena
	 * @var string
	 */
	public $domain;

	/**
	 * Wzorzec logowania (domyślnie %s)
	 * np. %s@example.com
	 * np. uid=%s,dc=example,dc=com
	 * @var string
	 */
	public $dnPattern = '%s';

}

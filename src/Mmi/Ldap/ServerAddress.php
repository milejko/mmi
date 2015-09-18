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
class ServerAddress {
	
	/**
	 * Protokół np. ldap, https itp. (domyślny ldap)
	 * @var string
	 */
	public $protocol = 'ldap';
	
	/**
	 * Adres hosta np. ldap.example.com
	 * @var string
	 */
	public $host;
	
	/**
	 * Port usługi (domyślny 389)
	 * @var integer
	 */
	public $port = 389;
	
}

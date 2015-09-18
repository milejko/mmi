<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Db;

class Config {

	/**
	 * Silnik bazy danych
	 * pgsql | mysql | sqlite
	 * @var string
	 */
	public $driver;

	/**
	 * Host bazy danych (lub ścieżka sqlite)
	 * @var string
	 */
	public $host;

	/**
	 * Port bazy danych
	 * @var integer
	 */
	public $port;
	
	/**
	 * Host tylko do zapisu w clusterze
	 * @var string
	 */
	public $upstreamHost;

	/**
	 * Port tylko do zapisu w clusterze
	 * @var integer
	 */
	public $upstreamPort;

	/**
	 * Nazwa bazy
	 * @var string
	 */
	public $name;

	/**
	 * Schemat
	 * @var string
	 */
	public $schema;

	/**
	 * Nazwa użytkownika
	 * @var string
	 */
	public $user;

	/**
	 * Hasło
	 * @var string
	 */
	public $password;

	/**
	 * Kodowanie znaków
	 * @var string
	 */
	public $charset = 'utf8';

	/**
	 * Połączenie trwałe
	 * @var boolean
	 */
	public $persistent = false;

	/**
	 * Profiler włączony
	 * @var boolean
	 */
	public $profiler = false;

}

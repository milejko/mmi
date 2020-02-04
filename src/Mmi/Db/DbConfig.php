<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Db;

/**
 * Class DbConfig
 * @package Mmi\Db
 *
 * @deprecated since 3.8 to be removed in 4.0
 */
class DbConfig
{

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

}

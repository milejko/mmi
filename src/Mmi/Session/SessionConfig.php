<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Session;

/**
 * Klasa konfiguracji sesji
 */
class SessionConfig
{

    /**
     * Nazwa sesji
     * @var string
     */
    public $name;

    /**
     * Czas życia cookie w sekundach (0 - bez limitu)
     * @var int
     */
    public $cookieLifetime = 0;

    /**
     * Ścieżka cookie
     * @var string
     */
    public $cookiePath = '/';

    /**
     * Domena cookie
     * @var string
     */
    public $cookieDomain = '';

    /**
     * Cookie secure
     * @var bool
     */
    public $cookieSecure = false;

    /**
     * HTTP only cookie
     * @var bool
     */
    public $cookieHttpOnly = false;

    /**
     * Czas wygasania cache w sekundach
     * @var int
     */
    public $cacheExpire = 14400;

    /**
     * Mnożnik GC
     * @var int
     */
    public $gcDivisor = 1000;

    /**
     * Czas życia GC
     * @var int
     */
    public $gcMaxLifetime = 28800;

    /**
     * Prawdopodobnieństwo zadziałania GC
     * @var int
     */
    public $gcProbability = 1;

    /**
     * Backend obsługujący sesje
     * files | memcache
     * @var string
     */
    public $handler = 'files';

    /**
     * Ścieżka zapisu sesji
     * @var tmp
     */
    public $path = '/tmp';

    /**
     * Orm autoryzacyjny
     * @var string
     */
    public $authModel;

    /**
     * Czas pamiętania użytkownika
     * @var int
     */
    public $authRemember = 31536000;

}

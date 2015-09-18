<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Session;

class Config {

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
	public $path;

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

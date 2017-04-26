<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\App;

/**
 * Abstrakcyjna klasa rejestru aplikacji
 * powinna być rozszerzona przez \App\Registry
 */
abstract class KernelRegistry
{

    /**
     * Konfiguracja aplikacji
     * @var \App\Config
     */
    public static $config;

    /**
     * Obiekt bufora
     * @var \Mmi\Cache\Cache
     */
    public static $cache;

    /**
     * Obiekt adaptera bazodanowego
     * @var \Mmi\Db\Adapter\PdoAbstract
     */
    public static $db;

}

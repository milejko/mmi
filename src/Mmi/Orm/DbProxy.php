<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Orm;

use Mmi\App\App;
use Mmi\Db\DbInformationInterface;
use Mmi\Db\DbInterface;

/**
 * Proxy to DbInterface and DbInformationInterface
 * This proxy is currently needed as PDO does not allow serializing and by extend to this - caching
 */
class DbProxy
{

    /**
     * Returns DbInterface
     */
    public static function getDb(): DbInterface
    {
        return App::$di->get(DbInterface::class);
    }

    /**
     * Returns DbInformationInterface
     */
    public static function getDbInformation(): DbInformationInterface
    {
        return App::$di->get(DbInformationInterface::class);
    }

}
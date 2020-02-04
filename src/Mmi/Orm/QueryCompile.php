<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Orm;

/**
 * Skompilowane zapytanie
 *
 * @deprecated since 3.8 to be removed in 4.0
 */
class QueryCompile
{

    /**
     * Część FROM zapytania
     * @var string
     */
    public $from;

    /**
     * Część WHERE zapytania
     * @var string
     */
    public $where;

    /**
     * Część ORDER zapytania
     * @var string
     */
    public $order = '';

    /**
     * Tablica wartości where dla PDO::prepare()
     * @see PDO::prepare()
     * @var array
     */
    public $bind = [];

    /**
     * Limit
     * @var int
     */
    public $limit;

    /**
     * Offset
     * @var int
     */
    public $offset;

    /**
     * Grupowanie
     * @var string
     */
    public $groupBy;

    /**
     * Schemat połączeńs
     * @var array
     */
    public $joinSchema = [];

}

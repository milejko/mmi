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
 * Rekord sesji
 *
 * @deprecated since 3.8 to be removed in 4.0
 */
class SessionRecord extends \Mmi\Orm\Record
{

    /**
     * Klucz
     * @var string
     */
    public $id;

    /**
     * Dane (mediumtext)
     * @var string
     */
    public $data;

    /**
     * Timestamp
     * @var integer
     */
    public $timestamp;

}

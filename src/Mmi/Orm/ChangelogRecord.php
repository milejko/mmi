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
 * Rekord incrementala bazy danych
 */
class ChangelogRecord extends \Mmi\Orm\Record
{

    /**
     * Klucz
     * @var int
     */
    public $id;
    
    /**
     * Nazwa pliku
     * @var string 
     */
    public $filename;
    
    /**
     * Odcisk zawartości pliku
     * @var string 
     */
    public $md5;

    /**
     * @var bool
     */
    public $active;

}

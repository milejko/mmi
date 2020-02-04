<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Orm;

use Mmi\Db\DbException;

/**
 * Rekord cache
 *
 * @deprecated since 3.8 to be removed in 4.0
 */
class CacheRecord extends \Mmi\Orm\Record
{

    /**
     * Klucz
     * @var string
     */
    public $id;

    /**
     * Dane (longblob)
     * @var string
     */
    public $data;

    /**
     * TTL
     * @var integer
     */
    public $ttl;
    
    /**
     * Zapis to próba wstawienia, przy niepowodzeniu - update
     * @return boolean
     */
    public function save()
    {
        //próba wstawienia - częstsza operacja
        try {
            //insert
            return $this->_insert();
        } catch (DbException $e) {
            //próba aktualizacji
            try {
                //update
                return $this->_update();
            } catch (DbException $e) {
                return false;
            }
        }
    }

}

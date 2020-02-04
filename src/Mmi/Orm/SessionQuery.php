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
 * Zapytanie dla rekordów sesji
 *
 * @deprecated since 3.8 to be removed in 4.0
 */
class SessionQuery extends \Mmi\Orm\Query
{

    /**
     * Nazwa tabeli
     * @var string
     */
    protected $_tableName = 'mmi_session';

}

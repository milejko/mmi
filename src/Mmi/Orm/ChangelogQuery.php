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
 * Zapytanie używane przy wdrożeniach incrementali bazy danych
 *
 * @deprecated since 3.8 to be removed in 4.0
 */
class ChangelogQuery extends \Mmi\Orm\Query
{

    protected $_tableName = 'mmi_changelog';

}

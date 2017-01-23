<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Cache\Orm;

/**
 * Zapytanie dla rekordów cache
 */
class DbCacheQuery extends \Mmi\Orm\Query {

	/**
	 * Nazwa tabeli
	 * @var string
	 */
	protected $_tableName = 'DB_CACHE';

}

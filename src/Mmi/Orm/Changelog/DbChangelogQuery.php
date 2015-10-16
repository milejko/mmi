<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Orm\Changelog;

/**
 * QUERY dla loga używanego przy wdrożeniach incrementali bazy danych
 */
class DbChangelogQuery extends \Mmi\Orm\Query {

	protected $_tableName = 'DB_CHANGELOG';

	/**
	 * Zapytanie szukające po nazwie pliku
	 * @param string $filename
	 * @return \App\Orm\Changelog
	 */
	public static function byFilename($filename) {
		return (new self)
				->whereFilename()->equals($filename);
	}

}

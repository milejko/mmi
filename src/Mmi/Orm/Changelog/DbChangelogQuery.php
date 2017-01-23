<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Orm\Changelog;

/**
 * Zapytanie dla loga używanego przy wdrożeniach incrementali bazy danych
 */
class DbChangelogQuery extends \Mmi\Orm\Query {

	/**
	 * Nazwa tabeli
	 * @var string
	 */
	protected $_tableName = 'DB_CHANGELOG';

	/**
	 * Zapytanie szukające po nazwie pliku
	 * @param string $filename
	 * @return \Mmi\Orm\Changelog\DbChangelogQuery
	 */
	public function byFilename($filename) {
		return $this->whereFilename()->equals($filename);
	}

}

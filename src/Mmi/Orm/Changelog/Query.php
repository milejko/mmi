<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Orm\Changelog;

/**
 * QUERY dla loga używanego przy wdrożeniach incrementali bazy danych
 */
class Query extends \Mmi\Orm\Query {

	protected $_tableName = 'DB_CHANGELOG';

	/**
	 * Zwraca instancję siebie
	 * @return \App\Orm\Changelog\Query
	 */
	public static function factory($tableName = null) {
		//nowy obiekt swojej klasy
		return new self($tableName);
	}

	/**
	 * Zapytanie szukające po nazwie pliku
	 * @param string $filename
	 * @return \App\Orm\Changelog
	 */
	public static function byFilename($filename) {
		return self::factory()
				->whereFilename()->equals($filename);
	}

}

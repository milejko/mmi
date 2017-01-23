<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Orm;

/**
 * Zapytanie używane przy wdrożeniach incrementali bazy danych
 */
class ChangelogQuery extends \Mmi\Orm\Query {

	protected $_tableName = 'mmi_changelog';

	/**
	 * Zapytanie szukające po nazwie pliku
	 * @param string $filename
	 * @return \App\Orm\Changelog
	 */
	public function byFilename($filename) {
		return $this->whereFilename()->equals($filename);
	}

}

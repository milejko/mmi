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
 * Rekord incrementala bazy danych
 */
class DbChangelogRecord extends \Mmi\Orm\Record {

	public $filename;
	public $md5;

}

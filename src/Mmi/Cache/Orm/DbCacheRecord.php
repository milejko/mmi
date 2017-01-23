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
 * Rekord cache
 */
class DbCacheRecord extends \Mmi\Orm\Record {

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
	
}

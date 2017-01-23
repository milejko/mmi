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
 * Rekord sesji
 */
class SessionRecord extends \Mmi\Orm\Record {

	/**
	 * Klucz
	 * @var string
	 */
	public $id;
	
	/**
	 * Dane (mediumtext)
	 * @var string
	 */
	public $data;
	
	/**
	 * Timestamp
	 * @var integer
	 */
	public $timestamp;

}

<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Orm;

/**
 * Skompilowane zapytanie
 */
class QueryCompile {

	/**
	 * Część FROM zapytania
	 * @var string
	 */
	public $from;

	/**
	 * Część WHERE zapytania
	 * @var string
	 */
	public $where;

	/**
	 * Część ORDER zapytania
	 * @var string
	 */
	public $order = '';

	/**
	 * Tablica wartości where dla PDO::prepare()
	 * @see PDO::prepare()
	 * @var array
	 */
	public $bind = [];

	/**
	 * Limit
	 * @var int
	 */
	public $limit;

	/**
	 * Offset
	 * @var int
	 */
	public $offset;
	
	/**
	 * Grupowanie
	 * @var string
	 */
	public $groupBy;

	/**
	 * Schemat połączeńs
	 * @var array
	 */
	public $joinSchema = [];

}

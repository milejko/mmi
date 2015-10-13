<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Cache;

/**
 * Klasa rejestru bufora
 */
class CacheRegistry extends \Mmi\OptionObject {
	
	/**
	 * Instancja rejestru
	 * @var self
	 */
	protected static $_instance;
	
	/**
	 * Pobiera instancję rejestru
	 * @return self
	 */
	public static function getInstance() {
		if (null === self::$_instance) {
			return self::$_instance = new self();
		}
		return self::$_instance;
	}
	
}
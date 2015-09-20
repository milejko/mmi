<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Db;

class Component {

	/**
	 * Tworzy obiekty adaptera na podstawie opcji
	 * @param \Mmi\Db\Config $config
	 * @return \Mmi\Db\Adapter\Pdo\PdoAbstract
	 */
	public static function factory(\Mmi\Db\Config $config) {
		if ($config->driver != 'mysql' && $config->driver != 'pgsql' && $config->driver != 'sqlite' && $config->driver != 'oci') {
			throw new \Exception('\Mmi\Db driver not supplied');
		}
		$driver = '\\Mmi\\Db\\Adapter\\Pdo\\' . ucfirst($config->driver);
		return new $driver($config);
	}

}

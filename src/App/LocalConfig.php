<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace App;

/**
 * Lokalna konfiguracja aplikacji
 */
class LocalConfig extends KernelConfig {

	public function __construct() {
		parent::__construct();
		//domyślnie włączony debug i kompilacja + wyłączenie cache
		$this->debug = true;
		$this->compile = true;
		$this->cache->active = false;
	}

}

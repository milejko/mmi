<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\App;

interface BootstrapInterface {

	/**
	 * Parametryzowanie bootstrapa
	 * @param string $env nazwa środowiska
	 */
	public function __construct($env);

	/**
	 * Uruchomienie bootstrapa
	 */
	public function run();
}

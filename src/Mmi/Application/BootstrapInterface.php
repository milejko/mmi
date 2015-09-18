<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Application;

interface BootstrapInterface {

	/**
	 * Parametryzowanie bootstrapa
	 */
	public function __construct();

	/**
	 * Uruchomienie bootstrapa
	 */
	public function run();
}

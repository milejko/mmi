<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Controller\Plugin;

class PluginAbstract {

	/**
	 * Metoda wykona się przed routingiem
	 * @param \Mmi\Controller\Request $request 
	 */
	public function routeStartup(\Mmi\Controller\Request $request) {
		
	}

	/**
	 * Metoda wykona się przed dispatchowaniem
	 * @param \Mmi\Controller\Request $request
	 */
	public function preDispatch(\Mmi\Controller\Request $request) {
		
	}

	/**
	 * Metoda wykona się po dispatchowaniu
	 * @param \Mmi\Controller\Request $request
	 */
	public function postDispatch(\Mmi\Controller\Request $request) {
		
	}

}

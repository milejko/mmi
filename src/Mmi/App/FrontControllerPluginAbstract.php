<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\App;

class FrontControllerPluginAbstract {

	/**
	 * Metoda wykona się przed routingiem
	 * @param \Mmi\Http\Request $request 
	 */
	public function routeStartup(\Mmi\Http\Request $request) {
		
	}

	/**
	 * Metoda wykona się przed dispatchowaniem
	 * @param \Mmi\Http\Request $request
	 */
	public function preDispatch(\Mmi\Http\Request $request) {
		
	}

	/**
	 * Metoda wykona się po dispatchowaniu
	 * @param \Mmi\Http\Request $request
	 */
	public function postDispatch(\Mmi\Http\Request $request) {
		
	}

}

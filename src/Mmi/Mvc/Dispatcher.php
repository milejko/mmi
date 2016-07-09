<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Mvc;

use Mmi\App\FrontController;

/**
 * Klasa dispatchera
 */
class Dispatcher {

	/**
	 * Uruchamianie metody routeStartup na zarejestrowanych pluginach
	 */
	public function routeStartup() {
		foreach (FrontController::getInstance()->getPlugins() as $plugin) {
			//wykonywanie routeStartup() na kolejnych pluginach
			$plugin->routeStartup(FrontController::getInstance()->getRequest());
		}
	}

	/**
	 * Uruchamianie metody preDispatch na zarejestrowanych pluginach
	 */
	public function preDispatch() {
		foreach (FrontController::getInstance()->getPlugins() as $plugin) {
			//wykonywanie preDispatch() na kolejnych pluginach
			$plugin->preDispatch(FrontController::getInstance()->getRequest());
		}
	}

	/**
	 * Uruchamianie metody postDispatch na zarejestrowanych pluginach
	 */
	public function postDispatch() {
		foreach (FrontController::getInstance()->getPlugins() as $plugin) {
			//wykonywanie postDispatch() na kolejnych pluginach
			$plugin->postDispatch(FrontController::getInstance()->getRequest());
		}
	}

	/**
	 * Dispatcher
	 * @return string
	 */
	public function dispatch() {
		//wpięcie dla pluginów przed routingiem
		$this->routeStartup();
		$fc = FrontController::getInstance();
		FrontController::getInstance()->getProfiler()->event('Mvc\Dispatcher: plugins route startup');
		//stosowanie routingu jeśli request jest pusty
		if (!$fc->getRequest()->getModuleName()) {
			$fc->getRouter()->processRequest($fc->getRequest());
		}
		//new relic
		extension_loaded('newrelic') ? newrelic_name_transaction($fc->getRequest()->module . '/' . $fc->getRequest()->controller . '/' . $fc->getRequest()->action) : null;
		FrontController::getInstance()->getProfiler()->event('Mvc\Dispatcher: routing applied');
		//wpięcie dla pluginów przed dispatchem
		$this->preDispatch();
		FrontController::getInstance()->getProfiler()->event('Mvc\Dispatcher: plugins pre-dispatch');
		//wybór i uruchomienie kontrolera akcji
		$content = \Mmi\Mvc\ActionHelper::getInstance()->action($fc->getRequest()->toArray());
		//wpięcie dla pluginów po dispatchu
		$this->postDispatch();
		FrontController::getInstance()->getProfiler()->event('Mvc\Dispatcher: plugins post-dispatch');
		return $content;
	}

}

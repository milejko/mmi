<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Mvc;

use Mmi\App\FrontController;

/**
 * Klasa dispatchera
 */
class Dispatcher
{

    /**
     * Uruchamianie metody routeStartup na zarejestrowanych pluginach
     */
    public function routeStartup()
    {
        //iteracja po pluginach
        foreach (FrontController::getInstance()->getPlugins() as $plugin) {
            //wykonywanie routeStartup() na kolejnych pluginach
            $plugin->routeStartup(FrontController::getInstance()->getRequest());
        }
        //profiler
        FrontController::getInstance()->getProfiler()->event('Mvc\Dispatcher: plugins route startup');
    }

    /**
     * Uruchamianie metody preDispatch na zarejestrowanych pluginach
     */
    public function preDispatch()
    {
        //iteracja po pluginach
        foreach (FrontController::getInstance()->getPlugins() as $plugin) {
            //wykonywanie preDispatch() na kolejnych pluginach
            $plugin->preDispatch(FrontController::getInstance()->getRequest());
        }
        //profiler
        FrontController::getInstance()->getProfiler()->event('Mvc\Dispatcher: plugins pre-dispatch');
    }

    /**
     * Dispatcher
     * @return string
     */
    public function dispatch()
    {
        //wpięcie dla pluginów przed routingiem
        $this->routeStartup();
        $frontController = FrontController::getInstance();
        //ustawianie requestu po zdekodowaniu żądania przez router
        $frontController->getRequest()
            ->setParams($frontController->getRouter()->decodeUrl($frontController->getEnvironment()->requestUri));
        //informacja o zakończeniu ustawiania routingu
        $frontController->getProfiler()->event('Mvc\Dispatcher: routing applied');
        //wpięcie dla pluginów przed dispatchem
        $this->preDispatch();
        //ustawianie contentu po wykonaniu requestu
        return $frontController->getResponse()->setContent(\Mmi\Mvc\ActionHelper::getInstance()->forward($frontController->getRequest()));
    }

}

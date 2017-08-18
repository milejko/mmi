<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
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
     * Uruchamianie metody postDispatch na zarejestrowanych pluginach
     */
    public function postDispatch()
    {
        //iteracja po pluginach
        foreach (FrontController::getInstance()->getPlugins() as $plugin) {
            //wykonywanie postDispatch() na kolejnych pluginach
            $plugin->postDispatch(FrontController::getInstance()->getRequest());
        }
        //profiler
        FrontController::getInstance()->getProfiler()->event('Mvc\Dispatcher: plugins post-dispatch');
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
        //stosowanie routingu
        $frontController->getRouter()->processRequest($frontController->getRequest());
        //informacja o zakończeniu ustawiania routingu
        $frontController->getProfiler()->event('Mvc\Dispatcher: routing applied');
        //wpięcie dla pluginów przed dispatchem
        $this->preDispatch();
        //wybór i uruchomienie kontrolera akcji
        $content = \Mmi\Mvc\ActionHelper::getInstance()->action($frontController->getRequest(), true);
        //wpięcie dla pluginów po dispatchu
        $this->postDispatch();
        //zwrot contentu z layoutem
        return \Mmi\Mvc\ActionHelper::getInstance()->layout($content, $frontController->getRequest());
    }

}

<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\App;

/**
 * Bootstrap aplikacji CMD
 */
class BootstrapCli extends \Mmi\App\Bootstrap
{

    /**
     * Konstruktor, ustawia ścieżki, ładuje domyślne klasy, ustawia autoloadera
     */
    public function __construct()
    {
        \App\Registry::$config->session = null;
        parent::__construct();
    }

    /**
     * Uruchamianie bootstrapa - brak front kontrolera
     */
    public function run()
    {
        $request = new \Mmi\Http\Request;
        //ustawianie domyślnego języka jeśli istnieje
        if (isset(\App\Registry::$config->languages[0])) {
            $request->lang = \App\Registry::$config->languages[0];
        }
        $request->setModuleName('mmi')
            ->setControllerName('index')
            ->setActionName('index');
        //ustawianie żądania
        FrontController::getInstance()->setRequest($request)
            ->getView()->setRequest($request);
        FrontController::getInstance()->getResponse()->clearHeaders();
    }

}

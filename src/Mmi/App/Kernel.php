<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\App {

    /**
     * Klasa jądra aplikacji
     */
    class Kernel
    {

        /**
         * Obiekt bootstrap
         * @var \Mmi\App\BootstrapInterface
         */
        private $_bootstrap;

        /**
         * Konstruktor
         */
        public function __construct($bootstrapName = '\Mmi\App\Bootstrap', $env = 'DEV')
        {
            //ładownie konfiguracji
            $this->_initConfig($env);
            //start aplikacji
            FrontController::getInstance()->getProfiler()->event('App\Kernel: application startup');
            //inicjalizacja aplikacji
            $this->_initEventHandler()
                ->_initEncoding();
            //bootstrap start
            FrontController::getInstance()->getProfiler()->event('App\Kernel: bootstrap startup');
            //tworzenie instancji bootstrapa
            $this->_bootstrap = new $bootstrapName($env);
            //bootstrap end
            FrontController::getInstance()->getProfiler()->event('App\Kernel: bootstrap done');
            //bootstrap nie implementuje właściwego interfeace'u
            if (!($this->_bootstrap instanceof \Mmi\App\BootstrapInterface)) {
                throw new KernelException('\Mmi\App bootstrap should be implementing \Mmi\App\Bootstrap\Interface');
            }
        }

        /**
         * Uruchomienie aplikacji
         */
        public function run()
        {
            $this->_bootstrap->run();
        }

        /**
         * Ustawia konfigurację
         * @param string $env
         * @return Kernel
         * @throws KernelException
         */
        private function _initConfig($env)
        {
            //konwencja nazwy konfiguracji
            $configClassName = '\App\Config' . $env;
            //sprawdzenie czy klasa konfiguracji istnieje
            if (!class_exists($configClassName)) {
                throw new \Mmi\App\KernelException('There is no environment configuration class');
            }
            //konfiguracja dla danego środowiska
            \App\Registry::$config = new $configClassName;
            //strefa czasowa
            date_default_timezone_set(\App\Registry::$config->timeZone);
            //ustawianie konfiguracji loggera
            \App\Registry::$config->log ? \Mmi\Log\LoggerHelper::setConfig(\App\Registry::$config->log) : null;
            //włączenie profilera
            FrontController::getInstance()->setProfiler(new KernelProfiler);
            return $this;
        }

        /**
         * Ustawia kodowanie na UTF-8
         * @return \Mmi\App\Kernel
         */
        private function _initEncoding()
        {
            //wewnętrzne kodowanie znaków
            mb_internal_encoding('utf-8');
            //domyślne kodowanie znaków PHP
            ini_set('default_charset', 'utf-8');
            //locale
            setlocale(LC_ALL, 'pl_PL.utf-8');
            setlocale(LC_NUMERIC, 'en_US.UTF-8');
            //ustawienie lokalizacji
            ini_set('default_charset', \App\Registry::$config->charset);
            return $this;
        }

        /**
         * Ustawia handler zdarzeń PHP
         * @return \Mmi\App\Kernel
         */
        private function _initEventHandler()
        {
            new KernelEventHandler;
            return $this;
        }

    }

}

namespace {

    /**
     * Globalna funkcja zrzucająca zmienną
     * @param mixed $var
     */
    function dump($var)
    {
        echo '<pre>' . print_r($var, true) . '</pre>';
    }

}
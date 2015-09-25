<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\App {

	class Kernel {

		/**
		 * Obiekt bootstrap
		 * @var \Mmi\App\BootstrapInterface
		 */
		private $_bootstrap;

		/**
		 * Konstruktor
		 */
		public function __construct($bootstrapName = '\Mmi\App\Bootstrap', $env = 'DEV') {
			//inicjalizacja aplikacji
			$this->_initPaths()
				->_initEncoding()
				->_initErrorHandler();
			\Mmi\Profiler::event('App: bootstrap startup');
			//tworzenie instancji bootstrapa
			$this->_bootstrap = new $bootstrapName($env);
			\Mmi\Profiler::event('App: bootstrap executed');
			//bootstrap nie implementuje właściwego interfeace'u
			if (!($this->_bootstrap instanceof \Mmi\App\BootstrapInterface)) {
				throw new Exception('\Mmi\App bootstrap should be implementing \Mmi\App\Bootstrap\Interface');
			}
		}

		/**
		 * Uruchomienie aplikacji
		 * @param \Mmi\Bootstrap $bootstrap
		 */
		public function run() {
			$this->_bootstrap->run();
		}

		/**
		 * Ustawia kodowanie na UTF-8
		 * @return \Mmi\App\Kernel
		 */
		protected function _initEncoding() {
			//wewnętrzne kodowanie znaków
			mb_internal_encoding('utf-8');
			//domyślne kodowanie znaków PHP
			ini_set('default_charset', 'utf-8');
			//locale
			setlocale(LC_ALL, 'pl_PL.utf-8');
			setlocale(LC_NUMERIC, 'en_US.UTF-8');
			return $this;
		}

		/**
		 * Definicja ścieżek
		 * @param string $systemPath
		 * @return \Mmi\App\Kernel
		 */
		protected function _initPaths() {
			//pierwszy event profilera
			\Mmi\Profiler::event('App: startup');
			//zasoby publiczne
			define('PUBLIC_PATH', BASE_PATH . '/web');
			//dane
			define('DATA_PATH', BASE_PATH . '/var/data');
			return $this;
		}

		/**
		 * Ustawia handler błędów
		 * @return \Mmi\App\Kernel
		 */
		protected function _initErrorHandler() {
			//domyślne przechwycenie wyjątków
			set_exception_handler(['\Mmi\App\ErrorHandler', 'exceptionHandler']);
			//domyślne przechwycenie błędów
			set_error_handler(['\Mmi\App\ErrorHandler', 'errorHandler']);
			return $this;
		}

	}

}

namespace {

	/**
	 * Globalna funkcja zrzucająca zmienną
	 * @param mixed $var
	 */
	function dump($var) {
		echo '<pre>' . print_r($var, true) . '</pre>';
	}

}
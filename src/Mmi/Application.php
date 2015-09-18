<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi {

	class Application {

		/**
		 * Obiekt bootstrap
		 * @var \Mmi\Application\BootstrapInterface
		 */
		private $_bootstrap;

		/**
		 * Konstruktor
		 * @param string $path
		 */
		public function __construct($bootstrapName = '\Mmi\Application\Bootstrap') {
			//inicjalizacja aplikacji
			$this->_initPaths()
				->_initEncoding()
				->_initPhpConfiguration()
				->_initErrorHandler();
			//tworzenie instancji bootstrapa
			$this->_bootstrap = new $bootstrapName();
			\Mmi\Profiler::event('Application: bootstrap executed');
			//bootstrap nie implementuje właściwego interfeace'u
			if (!($this->_bootstrap instanceof \Mmi\Application\BootstrapInterface)) {
				throw new \Exception('\Mmi\Application bootstrap should be implementing \Mmi\Application\Bootstrap\Interface');
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
		 * @return \Mmi\Application
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
		 * @return \Mmi\Application
		 */
		protected function _initPaths() {
			//pierwszy event profilera
			\Mmi\Profiler::event('Application: startup');
			//zasoby publiczne
			define('PUBLIC_PATH', BASE_PATH . '/web');
			//dane
			define('DATA_PATH', BASE_PATH . '/var/data');
			//domyślna ścieżka ładowania (vendors)
			set_include_path(BASE_PATH . '/vendor');
			return $this;
		}

		/**
		 * Inicjalizacja konfiguracji PHP
		 * @return \Mmi\Application
		 */
		protected function _initPhpConfiguration() {
			//obsługa włączonych magic quotes
			if (ini_get('magic_quotes_gpc')) {
				throw new \Exception('\Mmi\Application: magic quotes enabled');
			}
			return $this;
		}

		/**
		 * Ustawia handler błędów
		 * @return \Mmi\Application
		 */
		protected function _initErrorHandler() {
			//domyślne przechwycenie wyjątków
			set_exception_handler(['\Mmi\Application\Error', 'exceptionHandler']);
			//domyślne przechwycenie błędów
			set_error_handler(['\Mmi\Application\Error', 'errorHandler']);
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
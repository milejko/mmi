<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Command;

/**
 * Abstrakcyjna klasa narzędzia linii komend
 */
abstract class CommandAbstract {

	/**
	 * Konstruktor konfiguruje środowisko dla linii komend
	 */
	public final function __construct($env) {
		//brak środowiska
		if (!$env) {
			throw new \Exception('No ENV defined');
		}
		//konfiguracja autoloadera
		$this->_setupAutoload();
		//brak limitu czasowego
		set_time_limit(0);
		//powołanie aplikacji
		$application = new \Mmi\App\Kernel('\Mmi\App\BootstrapCli', $env);
		//ustawienie typu odpowiedzi na plain
		\Mmi\App\FrontController::getInstance()->getResponse()->setTypePlain();
		//uruchomienie aplikacji
		$application->run();
	}

	/**
	 * Obliczanie ścieżki
	 * @return string
	 * @throws \Mmi\App\KernelException
	 */
	protected function _setupAutoload() {
		//dopuszczalne ścieżki /bin ; /src/Module/Tools ; /vendor/name/subname/src/Module/Tools
		foreach ([__DIR__ . '/..', __DIR__ . '/../../..', __DIR__ . '/../../../../../..'] as $path) {
			if (!file_exists($path . '/vendor/autoload.php')) {
				continue;
			}
			//określanie ścieżki
			define('BASE_PATH', $path . '/');
			//ładowanie autoloadera aplikacji
			include BASE_PATH . '/vendor/autoload.php';
			return;
		}
		//brak autoloadera
		throw new \Mmi\App\KernelException('Autoloader not found');
	}

	/**
	 * Metoda uruchamiająca narzędzie
	 */
	abstract public function run();

	/**
	 * Uruchomienie metoda run() przy destrukcji klasy
	 */
	public final function __destruct() {
		$this->run();
	}

}
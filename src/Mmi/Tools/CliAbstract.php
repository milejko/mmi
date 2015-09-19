<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Tools;

/**
 * Abstrakcyjna klasa narzędzia linii komend
 */
abstract class CliAbstract {

	/**
	 * Konstruktor konfiguruje środowisko dla linii komend
	 */
	public final function __construct() {
		//określanie ścieżki
		define('BASE_PATH', $this->_calculatePath());
		//ładowanie autoloadera aplikacji
		require BASE_PATH . '/vendor/autoload.php';

		//powołanie aplikacji
		$application = new \Mmi\App\Kernel('\Mmi\App\BootstrapCli');

		//ustawienie typu odpowiedzi na plain
		\Mmi\Controller\Front::getInstance()->getResponse()->setTypePlain();

		//uruchomienie aplikacji
		$application->run();
	}
	
	/**
	 * Obliczanie ścieżki
	 * @return string
	 * @throws \Exception
	 */
	protected function _calculatePath() {
		//dopuszczalne ścieżki /bin ; /src/Module/Tools ; /vendor/name/subname/src/Module/Tools
		$paths = [__DIR__ . '/..', __DIR__ . '/../../..', __DIR__ . '/../../../../../..'];
		//sprawdzanie poprawności ścieżek
		foreach ($paths as $path) {
			if (file_exists($path . '/vendor/autoload.php')) {
				return $path;
			}
		}
		//brak autoloadera
		throw new \Exception('Autoloader not found');
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

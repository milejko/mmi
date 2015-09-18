<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
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
		define('BASE_PATH', realpath(dirname(__FILE__) . '/../../../'));

		//ładowanie autoloadera aplikacji
		require BASE_PATH . '/app/autoload.php';

		//powołanie aplikacji
		$application = new \Mmi\Application('\Mmi\Application\BootstrapCli');

		//ustawienie typu odpowiedzi na plain
		\Mmi\Controller\Front::getInstance()->getResponse()->setTypePlain();

		//uruchomienie aplikacji
		$application->run();
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

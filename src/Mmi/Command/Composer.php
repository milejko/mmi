<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Command;

use Composer\Script\Event;

/**
 * Klasa 
 */
class Composer {

	/**
	 * Wymagane katalogi
	 * @var array
	 */
	protected static $_directories = [
		'var/cache',
		'var/compile',
		'var/data',
		'var/log',
		'var/session',
		'web',
		'src/App',
	];

	/**
	 * Pliki dystrybucyjne
	 * @var array
	 */
	protected static $_distFiles = [
		'src/Mmi/Resource/config/KernelConfig.php.dist' => 'src/App/KernelConfig.php',
		'src/Mmi/Resource/config/KernelConfigLocal.php.dist' => 'src/App/KernelConfigLocal.php',
		'src/Mmi/Resource/config/Registry.php.dist' => 'src/App/Registry.php',
		'src/Mmi/Resource/web/index.php.dist' => 'web/index.php',
		'src/Mmi/Resource/web/.htaccess.dist' => 'web/.htaccess',
	];

	/**
	 * Po aktualizacji
	 * @param Event $event
	 */
	public static function postUpdate(Event $event) {
		self::_initApp($event);
		//wdrożenie bazy danych
		(new \Mmi\Db\Deployer())->deploy();
	}

	/**
	 * Po instalacji
	 * @param Event $event
	 */
	public static function postInstall(Event $event) {
		self::_initApp($event);
		//wdrożenie bazy danych
		(new \Mmi\Db\Deployer())->deploy();
	}

	/**
	 * Inicjalizacja autoloadera
	 * @param Event $event
	 */
	protected static function _initApp(Event $event) {
		//określenie katalogu vendorów
		$vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
		//ustawianie ścieżki bazowej projektu
		define('BASE_PATH', $vendorDir . '/../');
		//tworzenie katalogów
		self::_createDirectories();
		//kopiowanie plików dist
		self::_copyDistFiles();
		//wczytanie autoloadera
		require $vendorDir . '/autoload.php';
		//powołanie aplikacji
		$application = new \Mmi\App\Kernel('\Mmi\App\BootstrapCli');
		//ustawienie typu odpowiedzi na plain
		\Mmi\App\FrontController::getInstance()->getResponse()->setTypePlain();
		//uruchomienie aplikacji
		$application->run();
	}

	/**
	 * Tworzy wymagane katalogi
	 */
	protected static function _createDirectories() {
		foreach (self::$_directories as $dir) {
			if (file_exists(BASE_PATH . $dir)) {
				continue;
			}
			mkdir(BASE_PATH . $dir, 0777, true);
		}
	}

	/**
	 * Kopiuje pliki z dystrybucji
	 */
	protected static function _copyDistFiles() {
		foreach (self::$_distFiles as $src => $dest) {
			//plik istnieje
			if (file_exists(BASE_PATH . $dest)) {
				continue;
			}
			//mmi jest w vendorach
			if (!file_exists(BASE_PATH . $src)) {
				$src = 'vendor/mmi/mmi/' . $src;
			}
			copy(BASE_PATH . $src, BASE_PATH . $dest);
		}
	}

}

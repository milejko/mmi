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
 * Klasa używana przy instalacji composerem
 */
class ComposerInstaller {

	/**
	 * Pliki dystrybucyjne
	 * @var array
	 */
	protected static $_distFiles = [
		'dist' => '',
	];

	/**
	 * Po aktualizacji
	 * @param Event $event
	 */
	public static function postUpdate(Event $event) {
		self::_initApp($event);
	}

	/**
	 * Po instalacji
	 * @param Event $event
	 */
	public static function postInstall(Event $event) {
		self::_initApp($event);
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
		//kopiowanie plików dist
		self::_copyDistFiles();
		//wczytanie autoloadera
		require $vendorDir . '/autoload.php';
		//powołanie aplikacji
		$application = new \Mmi\App\Kernel('\Mmi\App\BootstrapCli', 'DEV');
		//ustawienie typu odpowiedzi na plain
		\Mmi\App\FrontController::getInstance()->getResponse()->setTypePlain();
		//uruchomienie aplikacji
		$application->run();
	}

	/**
	 * Kopiuje pliki z dystrybucji
	 */
	protected static function _copyDistFiles() {
		//iteracja po wymaganych plikach
		foreach (self::$_distFiles as $src => $dest) {
			//kalkulacja ścieżki
			$source = BASE_PATH . $src;
			if (!file_exists($source)) {
				continue;
			}
			//kopiowanie katalogów
			\Mmi\FileSystem::copyRecursive($source, BASE_PATH . $dest, false);
			//usuwanie źródła
			\Mmi\FileSystem::rmdirRecursive($source);
			//usuwanie placeholderów
			\Mmi\FileSystem::unlinkRecursive('.placeholder', BASE_PATH . $dest);
		}
	}
	
}

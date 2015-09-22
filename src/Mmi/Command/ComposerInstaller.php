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
	 * Kopiuje pliki z dystrybucji
	 */
	protected static function _copyDistFiles() {
		//iteracja po wymaganych plikach
		foreach (self::$_distFiles as $src => $dest) {
			//kalkulacja ścieżki
			$source = self::_calculateDistPath($src);
			if (!file_exists($source)) {
				continue;
			}
			//kopiowanie pojedynczego pliku
			if (is_file($source)) {
				copy($src, BASE_PATH . $dest);
				continue;
			}
			//kopiowanie katalogów
			if (is_dir($source)) {
				//kopiowanie rekursywne
				\Mmi\FileSystem::copyRecursive($source, BASE_PATH . $dest);
			}
		}
	}
	
	/**
	 * Obliczenie ścieżki
	 * @param string $src
	 * @return string
	 */
	protected static function _calculateDistPath($src) {
		//ścieżka z src
		if (file_exists(BASE_PATH . $src)) {
			return BASE_PATH . $src;
		}
		//ścieżka z vendorów
		foreach (\Mmi\App\StructureParser::getVendors() as $vendor) {
			if (file_exists($vendor . '/../' . $src)) {
				return $vendor . '/../' . $src;
			}
		}
	}

}

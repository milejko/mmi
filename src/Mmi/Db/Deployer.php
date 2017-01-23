<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Db;

/**
 * Klasa wdrożeń incrementali bazy danych
 */
class Deployer {

	/**
	 * Metoda uruchamiająca
	 * @throws DbException
	 */
	public function deploy() {
		//wyłączenie cache
		\App\Registry::$config->cache->active = false;
		$incrementals = [];
		//iteracja po modułach aplikacji
		foreach (\Mmi\Mvc\StructureParser::getModules() as $module) {
			//moduł nie zawiera incrementali
			if (!file_exists($module . '/Resource/incremental/' . \App\Registry::$config->db->driver)) {
				continue;
			}
			//iteracja po incrementalach
			foreach (glob($module . '/Resource/incremental/' . \App\Registry::$config->db->driver . '/*.sql') as $file) {
				$incrementals[basename($file)] = $file;
			}
		}
		//sortowanie plików
		ksort($incrementals);
		//przywracanie incrementali
		foreach ($incrementals as $incremental) {
			$this->_importIncremental($incremental);
		}
	}

	/**
	 * Importuje pojedynczy plik
	 * @param string $file
	 */
	protected function _importIncremental($file) {
		//nazwa pliku
		$baseFileName = basename($file);

		//hash pliku
		$md5file = md5_file($file);

		//ustawianie domyślnych parametrów importu
		\App\Registry::$db->setDefaultImportParams();

		//pobranie rekordu
		try {
			$dc = (new \Mmi\Orm\Changelog\DbChangelogQuery)->byFilename(basename($file))->findFirst();
		} catch (\Exception $e) {
			echo 'INITIAL IMPORT.' . "\n";
			$dc = null;
		}

		//restore istnieje md5 zgodne
		if ($dc !== null && $dc->md5 == $md5file) {
			echo 'INCREMENTAL PRESENT: ' . $baseFileName . "\n";
			flush();
			return;
		}

		//restore istnieje md5 niezgodne - plik się zmienił - przerwanie importu
		if ($dc !== null) {
			die('INVALID MD5: ' . $baseFileName . ' --- VALID: ' . $md5file . " --- IMPORT TERMINATED!\n");
		}
		//import danych
		$this->_importSql($file);

		//resetowanie struktur tabeli
		\Mmi\Orm\DbConnector::resetTableStructures();

		//brak restore - zakłada nowy rekord
		$newDc = new \Mmi\Orm\Changelog\DbChangelogRecord;
		//zapis informacji o incrementalu
		$newDc->filename = $baseFileName;
		$newDc->md5 = $md5file;
		$newDc->save();

		//informacja na ekran
		echo 'RESTORE INCREMENTAL: ' . $baseFileName . "\n";
		flush();
	}

	/**
	 * Import pliku sql
	 * @param string $fileName nazwa pliku
	 */
	protected function _importSql($fileName) {
		//rozbicie zapytań po średniku i końcu linii
		foreach (explode(';' . PHP_EOL, file_get_contents($fileName)) as $query) {
			//wykonanie zapytania
			$this->_performQuery($query);
		}
	}

	/**
	 * Wykonanie pojedynczego zapytania
	 * @param string $query
	 */
	protected function _performQuery($query) {
		if (!trim($query)) {
			return;
		}
		//start transakcji
		\App\Registry::$db->beginTransaction();

		//quera jeśli błędna rollback i die, jeśli poprawna commit
		try {
			\App\Registry::$db->query($query);
			\App\Registry::$db->commit();
		} catch (\Mmi\Db\DbException $e) {
			\App\Registry::$db->rollBack();
			die($e->getMessage() . "\n");
		}
	}

}

<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Db;

use \Mmi\Orm;

/**
 * Klasa wdrożeń incrementali bazy danych
 */
class Deployer
{
    const TYPE_SQL = 'sql';
    const TYPE_PHP = 'php';

    /**
     * Metoda uruchamiająca
     * @throws DbException
     */
    public function deploy()
    {
        //wyłączenie bufora lokalnego
        \App\Registry::$config->localCache->active = false;
        //wyłączenie bufora aplikacji
        \App\Registry::$config->cache->active = false;
        //inicjalizacja tablicy inkrementali
        $incrementals = [];
        //iteracja po modułach aplikacji
        foreach (\Mmi\Mvc\StructureParser::getModules() as $module) {
            //moduł nie zawiera incrementali
            $directory = $module . '/Resource/incremental/' . \App\Registry::$config->db->driver;
            if (!file_exists($directory)) {
                continue;
            }
            //iteracja po incrementalach sql
            foreach (glob($directory . '/*.sql') as $file) {
                //dodawanie incrementala do tablicy
                $incrementals[basename($file)] = [
                    'fileType' => static::TYPE_SQL,
                    'fileName' => $file,
                ];
            }
            //iteracja po incrementalach php
            foreach (glob($directory . '/*.php') as $file) {
                //dodawanie incrementala do tablicy
                $incrementals[basename($file)] = [
                    'fileType' => static::TYPE_PHP,
                    'fileName' => $file,
                ];
            }
        }
        //sortowanie plików po nazwach
        ksort($incrementals);
        //przywracanie incrementali
        foreach ($incrementals as $incremental) {
            //importowanie incrementala
            $this->_importIncremental($incremental['fileType'], $incremental['fileName']);
            //flush wyniku na ekran
            flush();
        }
    }

    /**
     * Importuje pojedynczy plik
     * @param string $fileType
     * @param string $fileName
     * @throws \Mmi\App\KernelException
     */
    protected function _importIncremental($fileType, $fileName)
    {
        //nazwa pliku
        $baseFileName = basename($fileName);
        //hash pliku
        $md5file = md5_file($fileName);
        //ustawianie domyślnych parametrów importu
        \App\Registry::$db->setDefaultImportParams();
        //pobranie rekordu
        try {
            $dc = (new Orm\ChangelogQuery)->whereFilename()->equals(basename($fileName))->findFirst();
        } catch (\Exception $e) {
            echo 'INITIAL IMPORT.' . "\n";
            $dc = null;
        }
        //restore istnieje md5 zgodne
        if ($dc && $dc->md5 == $md5file) {
            echo 'INCREMENTAL PRESENT: ' . $baseFileName . "\n";
            return;
        }
        //restore istnieje md5 niezgodne - plik się zmienił - przerwanie importu
        if ($dc) {
            throw new \Mmi\App\KernelException('INVALID MD5: ' . $baseFileName . ' --- VALID: ' . $md5file . ' --- IMPORT TERMINATED!\n');
        }
        //informacja na ekran przed importem aby bylo wiadomo który
        echo 'RESTORE INCREMENTAL: ' . $baseFileName . "\n";
        //import danych z pliku sql
        if ($fileType === static::TYPE_SQL) {
            $this->_importSqlIncremental($fileName);
        } //import danych z pliku php
        elseif ($fileType === static::TYPE_PHP) {
            $this->_importPhpIncremental($fileName);
        }
        //resetowanie struktur tabeli
        \Mmi\Orm\DbConnector::resetTableStructures();
        //brak restore - zakłada nowy rekord
        $newDc = new Orm\ChangelogRecord;
        //zapis informacji o incrementalu
        $newDc->filename = $baseFileName;
        //wstawienie md5
        $newDc->md5 = $md5file;
        //zapis rekordu
        $newDc->save();
    }

    /**
     * Import pliku sql
     * @param string $fileName nazwa pliku
     * @throws DbException
     */
    protected function _importSqlIncremental($fileName)
    {
        //rozbicie zapytań po średniku i końcu linii
        foreach (explode(';' . PHP_EOL, file_get_contents($fileName)) as $query) {
            //wykonanie zapytania
            $this->_performQuery($query);
        }
    }

    /**
     * Wykonanie pojedynczego zapytania
     * @param string $query
     * @throws DbException
     */
    protected function _performQuery($query)
    {
        //brak query
        if (!trim($query)) {
            return;
        }
        //start transakcji
        \App\Registry::$db->beginTransaction();
        //quera jeśli błędna rollback i die, jeśli poprawna commit
        try {
            //wykonanie zapytania
            \App\Registry::$db->query($query);
            //commit
            \App\Registry::$db->commit();
        } catch (\Mmi\Db\DbException $e) {
            //rollback
            \App\Registry::$db->rollBack();
            throw $e;
        }
    }


    /**
     * Import pliku php
     * @param string $fileName nazwa pliku
     * @throws \Exception
     */
    protected function _importPhpIncremental($fileName)
    {
        require_once($fileName);

        // Nazwa klasy incrementala bazujaca na nazwie pliku
        $incrementalClass = 'Incremental_' . basename(strtolower($fileName), '.php');
        // Sprawdzenie czy klasa istnieje
        if (class_exists($incrementalClass)) {
            // Utworzenie instancji klasy incrementala
            $incremental = new $incrementalClass();
            // Sprawdzenie czy metoda execute istnieje
            if (method_exists($incremental, 'execute')) {
                // Start transakcji
                \App\Registry::$db->beginTransaction();
                try {
                    // Wykonanie incrementala
                    $incremental->execute(\App\Registry::$db);

                    // Commit
                    \App\Registry::$db->commit();
                } catch (\Exception $e) {
                    // Rollback
                    \App\Registry::$db->rollBack();
                    throw $e;
                }
            }
        }
    }
}

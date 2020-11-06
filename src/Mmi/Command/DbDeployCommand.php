<?php

namespace Mmi\Command;

use Mmi\Db\Adapter\PdoAbstract;
use Mmi\Orm\ChangelogQuery;
use Mmi\Orm\ChangelogRecord;
use Mmi\Orm\DbConnector;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Deploy database command
 */
class DbDeployCommand extends CommandAbstract
{

    /**
     * @var PdoAbstract
     */
    private $pdo;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Constructor
     */
    public function __construct(PdoAbstract $pdo, ContainerInterface $container)
    {
        $this->pdo          = $pdo;
        $this->container    = $container;
        parent::__construct();
    }

    /**
     * Metoda uruchamiająca
     * @throws DbException
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        //inicjalizacja tablicy inkrementali
        $incrementals = [];
        $driver = $this->container->get('db.driver');
        //iteracja po modułach aplikacji
        foreach (\Mmi\Mvc\StructureParser::getModules() as $module) {
            //moduł nie zawiera incrementali
            if (!file_exists($module . '/Resource/incremental/' . $driver)) {
                continue;
            }
            //iteracja po incrementalach
            foreach (glob($module . '/Resource/incremental/' . $driver . '/*.sql') as $file) {
                //dodawanie incrementala do tablicy
                $incrementals[basename($file)] = $file;
            }
        }
        //sortowanie plików po nazwach
        ksort($incrementals);
        //przywracanie incrementali
        foreach ($incrementals as $incremental) {
            //importowanie incrementala
            $this->_importIncremental($incremental);
        }
        return 0;
    }

    /**
     * Importuje pojedynczy plik
     * @param string $file
     * @throws \Mmi\App\KernelException
     */
    private function _importIncremental($file)
    {
        //nazwa pliku
        $baseFileName = basename($file);
        //hash pliku
        $md5file = md5_file($file);
        //ustawianie domyślnych parametrów importu
        $this->pdo->setDefaultImportParams();
        //pobranie rekordu
        try {
            $dc = (new ChangelogQuery())->whereFilename()->equals(basename($file))->findFirst();
        } catch (\Exception $e) {
            echo 'INITIAL IMPORT.' . $e->getMessage() . "\n";
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
        //import danych
        $this->_importSql($file);
        //resetowanie struktur tabeli
        DbConnector::resetTableStructures();
        //brak restore - zakłada nowy rekord
        $newDc = new ChangelogRecord();
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
     */
    private function _importSql($fileName)
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
     */
    private function _performQuery($query)
    {
        //brak query
        if (!trim($query)) {
            return;
        }
        //start transakcji
        $this->pdo->beginTransaction();
        //quera jeśli błędna rollback i exception, jeśli poprawna commit
        try {
            //wykonanie zapytania
            $this->pdo->query($query);
            //commit
            $this->pdo->commit();
        } catch (\Mmi\Db\DbException $e) {
            //rollback
            $this->pdo->rollBack();
            throw $e;
        }
    }

}
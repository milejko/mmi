<?php

namespace Mmi\Command;

use Mmi\Db\Adapter\PdoAbstract;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 
 */
class DaoRenderCommand extends CommandAbstract
{
    /**
     * @var PdoAbstract
     */
    private $pdo;

    /**
     * Constructor
     */
    public function __construct(PdoAbstract $pdo)
    {
        //injects
        $this->pdo = $pdo;
        parent::__construct();
    }

    /**
     * Execute command
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        //odbudowanie wszystkich DAO/Record/Query/Field/Join
        foreach ($this->pdo->tableList() as $tableName) {
            //buduje struktruę dla tabeli
            \Mmi\Orm\Builder::buildFromTableName($tableName);
        }
        $output->writeln('DAO classess rendered.');
        return 0;
    }

}
<?php

namespace Mmi\Command;

use Mmi\Db\DbInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 */
class DaoRenderCommand extends CommandAbstract
{
    /**
     * @var DbInterface
     */
    private $db;

    /**
     * Constructor
     */
    public function __construct(DbInterface $db)
    {
        //injects
        $this->db = $db;
        parent::__construct();
    }

    /**
     * Execute command
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        //odbudowanie wszystkich DAO/Record/Query/Field/Join
        foreach ($this->db->tableList() as $tableName) {
            //buduje struktruę dla tabeli
            \Mmi\Orm\Builder::buildFromTableName($tableName);
        }
        $output->writeln('DAO classess rendered.');
        return 0;
    }
}

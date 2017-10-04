<?php

namespace Mmi\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DaoRenderCommand extends CommandAbstract
{

    public function configure()
    {
        $this->setName('db:dao:render');
        $this->setDescription('Render DAO classess');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        //odbudowanie wszystkich DAO/Record/Query/Field/Join
        foreach (\App\Registry::$db->tableList(\App\Registry::$config->db->schema) as $tableName) {
            //buduje struktruę dla tabeli
            \Mmi\Orm\Builder::buildFromTableName($tableName);
        }

        $output->writeln('DAO classess rendered');
    }

}
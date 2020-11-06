<?php

namespace Mmi\Command;

use Symfony\Component\Console\Command\Command;

abstract class CommandAbstract extends Command
{

    public function configure()
    {
        $class = str_replace(['Command\\', 'Command', '\\'], ['', '', ':'], get_class($this));
        $this->setName($class);
        //$this->setDescription('Render DAO classess');
        parent::configure();
    }

}

<?php

namespace Mmi\Command;

use Symfony\Component\Console\Command\Command;

/**
 * Abstract command class
 */
abstract class CommandAbstract extends Command
{
    /**
     * Configure name and description
     */
    public function configure()
    {
        $class = str_replace(['Command\\', 'Command', '\\'], ['', '', ':'], get_class($this));
        $this->setName($class);
        $this->setDescription(get_class($this) . '::execute()');
        //$this->setDescription('Render DAO classess');
        parent::configure();
    }
}

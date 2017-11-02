<?php

namespace Mmi\Console;

use Mmi\Mvc\StructureParser;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends BaseApplication
{

    /**
     * Application constructor.
     */
    public function __construct()
    {
        parent::__construct('MMi Console', '1.0');

        $this->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED,
            'The Environment name.'));
    }

    /**
     * Bootstrap aplikacji przed wykonaniem komendy konsoli
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $env = $input->getParameterOption(['--env', '-e'], 'DEV');
        //powołanie aplikacji
        $app = new \Mmi\App\Kernel('\Mmi\App\BootstrapCli', $env);
        //ustawienie typu odpowiedzi na plain
        \Mmi\App\FrontController::getInstance()->getResponse()->setTypePlain();
        //uruchomienie aplikacji
        $app->run();

        return parent::doRun($input, $output);
    }

    /**
     * @return array
     */
    protected function getDefaultCommands()
    {
        return array_merge(parent::getDefaultCommands(), $this->getApplicationCommands());
    }

    /**
     * Pobieranie komend z aplikacji
     */
    protected function getApplicationCommands()
    {
        $commands = [];
        foreach (StructureParser::getModules() as $module) {
            //namespace modułu
            $moduleNamespace = substr($module, strrpos($module, DIRECTORY_SEPARATOR) + 1, strlen($module));
            //iteracja po komendach konsolowych
            foreach (glob($module . '/Console/*Command.php') as $command) {
                $className = basename($command, '.php');
                $class = '\\' . $moduleNamespace . '\\Console\\' . $className;

                //reflection do sprawdzenia pochodzenia
                $r = new \ReflectionClass($class);
                if ($r->isSubclassOf('Mmi\\Console\\CommandAbstract')
                    && !$r->isAbstract()
                    && !$r->getConstructor()->getNumberOfRequiredParameters()
                ) {
                    $commands[] = $r->newInstance();
                }
            }
        }
        return $commands;
    }

}
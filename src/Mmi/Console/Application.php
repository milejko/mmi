<?php

namespace Mmi\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends BaseApplication
{

    /**
     * Application constructor.
     * @param string $name
     * @param string $version
     */
    public function __construct()
    {
        parent::__construct('MMi Console', '1.0');

        $this->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.'));
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
}
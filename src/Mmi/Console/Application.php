<?php

namespace Mmi\Console;

use Mmi\App\App;
use Mmi\Http\Response;
use Mmi\Http\ResponseDebugger;
use Mmi\Mvc\StructureParser;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends BaseApplication
{

    /**
     * Application constructor.
     */
    public function __construct()
    {
        define(\BASE_PATH, realpath(__DIR__ . '../'));
        parent::__construct('MMi Console', '1.0');
    }

    /**
     * Bootstrap aplikacji przed wykonaniem komendy konsoli
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        //powołanie aplikacji
        $app = new App();
        //needed in case of an error (to be plain text)
        $app::$di->get(Response::class)->setTypePlain();
        //create App interceptor
        $output->writeln($app::$di->get(Response::class)->getContent());
        parent::doRun($input, $output);
        $app::$di->get(ResponseDebugger::class);
        $output->write($app::$di->get(Response::class)->getContent());
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
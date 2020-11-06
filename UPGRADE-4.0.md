FrontController::class is no longer available:

shortcut:
FrontController::getInstance()->getView() => App::$di->get(\Mmi\Mvc\View::class)
FrontController::getInstance()->getResponse() => App::$di->get(\Mmi\Http\Response::class)
FrontController::getInstance()->getRequest() => App::$di->get(\Mmi\Http\Request::class)
FrontController::getInstance()->getLogger() => App::$di->get(\Psr\Log\LoggerInterface::class)
FrontController::getInstance()->getEnvironment() => App::$di->get(\Mmi\Http\::class)

proper way:
use dependency injection(*)

Registry::class is no longer available:

shortcut:
Registry::$auth => App::$di->get(\Mmi\Security\Auth::class)
Registry::$cache => App::$di->get(\Mmi\Cache\Cache::class)
Registry::$db => App::$di->get(Mmi\Db\Adapter\PdoAbstract::class)
Registry::$config->skinset => App::$di->get(\Cms\App\CmsSkinsetConfig::class)

Registry::$config->others => no shortcuts - just get/inject proper value from the DI

proper way:
use dependency injection(*)

Request 

Commands (CLI) MUST be placed inside module/Command/ and have "Command" suffix folder
(ie. Cms/Command/SomeCommand.php) and also:
namespace Mmi\Console; => namespace Mmi\Command;
Mmi\Console\CommandAbstract => \Mmi\Command\CommandAbstract

Commands also are auto-named (by namespace and class name):
db:deploy => Mmi:DbDeploy
cms:cron:execute => Cms:CronExecute

Using dependency injection:
DI config files should name like di.php di.something.php and placed in the module main folder
(ie. Cms/di.translate.php, User/di.services.php)
Engine docs: https://php-di.org/doc/


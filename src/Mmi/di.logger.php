<?php

use Gelf\Publisher;
use Gelf\Transport\IgnoreErrorTransportWrapper;
use Gelf\Transport\UdpTransport;
use Mmi\App\AppProfilerInterface;
use Monolog\Handler\GelfHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

use function DI\env;

return [
    'log.level'     => env('LOG_LEVEL', Logger::DEBUG),
    'log.handler'   => env('LOG_HANDLER', ''),
    'log.path'      => env('LOG_PATH', ''),

    LoggerInterface::class => function (ContainerInterface $container) {
        $logger = new Logger('mmi');
        //default handler
        $logger->pushHandler(new StreamHandler(BASE_PATH . '/var/log/app.log', $container->get('log.level')));
        //no additional loger defined
        if (!$container->has('log.handler')) {
            return $logger;
        }
        //additional handler
        switch ($container->get('log.handler')) {
            //graylog
            case 'gelf':
                $pathPort = explode(':', $container->get('log.path'));
                $transport = new IgnoreErrorTransportWrapper(new UdpTransport($pathPort[0], isset($pathPort[1]) ? $pathPort[1] : 9000));
                $logger->pushHandler(new GelfHandler(new Publisher($transport), $container->get('log.level')));
                break;
            case 'stream':
                $logger->pushHandler(new StreamHandler($container->get('log.path'), $container->get('log.level')));
                break;
                //syslog
            case 'syslog':
                $logger->pushHandler(new SyslogHandler('mmi', LOG_USER, $container->get('log.level')));
                break;
        }
        $container->get(AppProfilerInterface::class)->event(LoggerInterface::class . ': logger setup');
        return $logger;
    },
];

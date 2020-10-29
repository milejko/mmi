<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return [
    LoggerInterface::class      => DI\factory(function (ContainerInterface $container) {
        $logger = new Logger('app');
        $logger->pushHandler(new StreamHandler($container->get('log.file', $container->get('log.level'))));
        return $logger;
    })
];
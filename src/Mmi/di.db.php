<?php

namespace Mmi\Db;

use Psr\Container\ContainerInterface;

use function DI\autowire;
use function DI\env;

return [
    //db env
    'db.driver'     => env('DB_DRIVER', 'mysql'),
    'db.password'   => env('DB_PASSWORD', ''),
    'db.host'       => env('DB_HOST', '127.0.0.1'),
    'db.name'       => env('DB_NAME', 'test'),
    'db.port'       => env('DB_PORT', 3306),
    'db.user'       => env('DB_USER', 'test'),

    //db profiler
    DbProfilerInterface::class => autowire(DbProfiler::class),

    //db service
    DbInterface::class => function (ContainerInterface $container) {
        //create db config
        $dbConfig = new DbConfig();
        //note: no upstream host/port supported here
        $dbConfig->driver   = $container->get('db.driver');
        $dbConfig->host     = $container->get('db.host');
        $dbConfig->name     = $container->get('db.name');
        $dbConfig->port     = $container->get('db.port');
        $dbConfig->user     = $container->get('db.user');
        $dbConfig->password = $container->get('db.password');
        //database not specified
        if (!$dbConfig->name || !$dbConfig->host) {
            return;
        }
        //compatible drivers
        if (!in_array($dbConfig->driver, ['mysql', 'sqlite'])) {
            throw new DbException('Unsupported driver: ' . $dbConfig->driver);
        }
        //obliczanie nazwy drivera
        $driver = '\\Mmi\\Db\\Adapter\\Pdo' . ucfirst($dbConfig->driver);
        //próba powołania drivera
        $db = new $driver($dbConfig);
        //set DB profiler
        $db->setProfiler($container->get(DbProfilerInterface::class));
        return $db;
    },

    //db information service
    DbInformationInterface::class => autowire(DbInformation::class),
];

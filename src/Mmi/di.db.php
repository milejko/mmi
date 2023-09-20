<?php

namespace Mmi\Db;

use Mmi\App\AppProfilerInterface;
use Psr\Container\ContainerInterface;

use function DI\autowire;
use function DI\env;

return [
    //db env
    'db.driver'         => env('DB_DRIVER', 'mysql'),
    'db.password'       => env('DB_PASSWORD', ''),
    'db.host'           => env('DB_HOST', '127.0.0.1'),
    'db.name'           => env('DB_NAME', 'test'),
    'db.port'           => env('DB_PORT', 3306),
    'db.user'           => env('DB_USER', 'test'),
    'db.upstream.host'  => env('DB_UPSTREAMHOST', env('DB_HOST', '127.0.0.1')),
    'db.upstream.port'  => env('DB_UPSTREAMPORT', env('DB_PORT', 3306)),

    //db profiler
    DbProfilerInterface::class => autowire(DbProfiler::class),

    //db service
    DbInterface::class => function (ContainerInterface $container) {
        //create db config
        $dbConfig = new DbConfig();
        $dbConfig->driver       = $container->get('db.driver');
        $dbConfig->host         = $container->get('db.host');
        $dbConfig->name         = $container->get('db.name');
        $dbConfig->port         = $container->get('db.port');
        $dbConfig->user         = $container->get('db.user');
        $dbConfig->password     = $container->get('db.password');
        $dbConfig->upstreamHost = $container->get('db.upstream.host') ?: $dbConfig->host;
        $dbConfig->upstreamPort = $container->get('db.upstream.port') ?: $dbConfig->port;
        //database not specified
        if (!$dbConfig->driver) {
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
        $container->get('app.debug.enabled') ? $db->setProfiler($container->get(DbProfilerInterface::class)) : null;
        $container->get(AppProfilerInterface::class)->event(DbInterface::class . ': database setup');
        return $db;
    },

    //db information service
    DbInformationInterface::class => autowire(DbInformation::class),
];

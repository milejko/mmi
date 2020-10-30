<?php

use Mmi\Db\Adapter\PdoAbstract;
use Mmi\Db\DbConfig;
use Psr\Container\ContainerInterface;

use function DI\env;

return [
    'db.driver'     => env('DB_DRIVER', 'mysql'),
    'db.password'   => env('DB_PASSWORD', ''),
    'db.host'       => env('DB_HOST', ''),
    'db.name'       => env('DB_PORT', ''),
    'db.port'       => env('DB_PORT', 3306),
    'db.user'       => env('DB_USER', ''),

    PdoAbstract::class => function (ContainerInterface $container) {
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
            throw new \Exception('Unsupported driver: ' . $dbConfig->driver);
        }
        //obliczanie nazwy drivera
        $driver = '\\Mmi\\Db\\Adapter\\Pdo' . ucfirst($dbConfig->driver);
        //próba powołania drivera
        $db = new $driver($dbConfig);
        //wstrzyknięcie profilera do adaptera bazodanowego
        $db->setProfiler(new \Mmi\Db\DbProfiler);
        //wstrzyknięcie do ORM
        \Mmi\Orm\DbConnector::setAdapter($db);
        return $db;
    },
];

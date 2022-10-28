<?php

namespace Mmi\Session;

use Mmi\App\AppProfilerInterface;
use Psr\Container\ContainerInterface;

use function DI\env;

return [
    'session.name'          => env('SESSION_NAME', 'PHPSESSID'),
    'session.path'          => env('SESSION_PATH', BASE_PATH . '/var/session'),
    'session.handler'       => env('SESSION_HANDLER', 'files'),
    'session.cookie.secure' => env('SESSION_COOKIE_SECURE', false),
    'session.cookie.http'   => env('SESSION_COOKIE_HTTP', false),

    SessionInterface::class => function (ContainerInterface $container) {
        //creating config
        $sessionConfig = new SessionConfig();
        $sessionConfig->name            = $container->get('session.name');
        $sessionConfig->handler         = $container->get('session.handler');
        $sessionConfig->path            = $container->get('session.path');
        $sessionConfig->cookieSecure    = $container->get('session.cookie.secure');
        $sessionConfig->cookieHttpOnly  = $container->get('session.cookie.http');
        $container->get(AppProfilerInterface::class)->event(SessionInterface::class . ': session setup');
        //creating session instance
        return new Session($sessionConfig);
    },
];

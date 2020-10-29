<?php

use Monolog\Logger;
use function DI\env;

return [
    'log.level' => env('LOG_LEVEL', Logger::DEBUG),
    'log.file'  => env('LOG_FILE', BASE_PATH . '/var/log/app.log')
];
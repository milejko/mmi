<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2020 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\App;

use Exception;

/**
 * Exception formatter
 */
class AppExceptionFormatter implements AppExceptionFormatterInterface
{

    /**
     * Formatuje obiekt wyjątku do pojedynczej wiadomości
     */
    public function formatException(Exception $exception): string
    {
        return str_replace(realpath(BASE_PATH), '', $exception->getMessage() . ' @ ' .
            $exception->getFile() . '(' . $exception->getLine() . ') ' .
            $this->formatTrace($exception));
    }

    /**
     * Format trace
     */
    public function formatTrace(Exception $exception): string
    {
        $message = '';
        $i = 0;
        $trace = $exception->getTrace();
        array_shift($trace);
        foreach ($trace as $row) {
            $i++;
            $message .= "\n" . '#' . $i;
            $message .= isset($row['file']) ? ' ' . $row['file'] : '';
            $message .= isset($row['line']) ? '(' . $row['line'] . ')' : '';
            $message .= isset($row['class']) ? ' ' . $row['class'] . '::' : '';
            $message .= isset($row['function']) ? (isset($row['class']) ? '' : ' ') . $row['function'] . '(' : '';
            $arguments = strip_tags(isset($row['args']) ? json_encode($row['args']) . ')' : '');
            $message .= (strlen($arguments) < 120) ? $arguments : '...)';
        }
        return $message;
    }
}

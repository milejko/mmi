<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\App;

use Monolog\Logger;

/**
 * Wyjątek aplikacji
 */
class KernelException extends \Exception
{

    /**
     * Poziom logowania
     * @var integer
     */
    protected $code = Logger::ERROR;

    /**
     * Pobiera sformatowaną wiadomość
     * @return string
     */
    public function getExtendedMessage()
    {
        $info = '';
        foreach ($this->getTrace() as $position) {
            if (isset($position['file'])) {
                $info .= ' ' . $position['file'] . '(' . $position['line'] . '): ' . $position['function'];
            }
        }
        $requestUri = \Mmi\App\FrontController::getInstance()->getEnvironment()->requestUri;
        return $requestUri . ' ' . strip_tags(parent::getMessage() . ': ' . $this->getFile() . '(' . $this->getLine() . ')' . $info);
    }

}

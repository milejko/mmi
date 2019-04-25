<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\App;

use Mmi\Log\LogConfigInstance;

/**
 * Wyjątek aplikacji
 *
 * @deprecated since 3.9.0 to be removed in 4.0.0
 */
class KernelException extends \Exception
{

    /**
     * Poziom logowania
     * @var integer
     */
    protected $code = LogConfigInstance::ERROR;

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

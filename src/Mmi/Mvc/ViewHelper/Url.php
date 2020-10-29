<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Mvc\ViewHelper;

class Url extends HelperAbstract
{

    /**
     * Generuje link na podstawie parametrów (z użyciem routera)
     * @see \Mmi\Mvc\Router::encodeUrl()
     * @param array $params parametry
     * @param boolean $reset nie łączy z bieżącym requestem
     * @return string
     */
    public function url(array $params = [])
    {
        //wyznaczanie url
        $url = $this->view->baseUrl . \Mmi\App\FrontController::getInstance()->getRouter()->encodeUrl($params);
        return $url ? $url : '/';
    }

}

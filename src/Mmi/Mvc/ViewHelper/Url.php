<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Mvc\ViewHelper;

use Mmi\App\App;
use Mmi\Http\Request;
use Mmi\Mvc\Router;

class Url extends HelperAbstract
{

    /**
     * Generuje link na podstawie parametrów (z użyciem routera)
     * @see \Mmi\Mvc\Router::encodeUrl()
     * @param array $params parametry
     * @param boolean $reset nie łączy z bieżącym requestem
     * @return string
     */
    public function url(array $params = [], $reset = true)
    {
        $urlParams = $reset ? $params : array_merge(App::$di->get(Request::class)->toArray(), $params);
        //wyznaczanie url
        $url = App::$di->get(Router::class)->encodeUrl($urlParams);
        return $url ? $url : '/';
    }

}

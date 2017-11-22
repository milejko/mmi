<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Mvc\ViewHelper;

class Widget extends HelperAbstract
{

    /**
     * Metoda główna, renderuje widget o zadanych parametrach
     * @param string $module moduł
     * @param string $controller kontroler
     * @param string $action akcja
     * @param array $params parametry
     * @return string
     */
    public function widget($module, $controller = 'index', $action = 'index', array $params = [])
    {
        return \Mmi\Mvc\ActionHelper::getInstance()->action((new \Mmi\Http\Request($params))
                ->setModuleName($module)
                ->setControllerName($controller)
                ->setActionName($action));
    }

}

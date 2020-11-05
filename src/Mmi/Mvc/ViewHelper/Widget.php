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
use Mmi\Mvc\ActionHelper;
use Mmi\Mvc\View;

class Widget extends HelperAbstract
{

    /**
     * @var ActionHelper
     */
    private $actionHelper;

    public function __construct(View $view, ActionHelper $actionHelper)
    {
        $this->actionHelper = $actionHelper;
        parent::__construct($view);
    }

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
        return $this->actionHelper->action((new \Mmi\Http\Request($params))
                ->setModuleName($module)
                ->setControllerName($controller)
                ->setActionName($action));
    }

}

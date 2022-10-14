<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Mvc\ViewHelper;

use Mmi\App\TestApp;
use Mmi\Filter\Escape as EscapeFilter;
use Mmi\Mvc\ViewHelper\Escape;
use Mmi\Mvc\View;

class EscapeTest extends \PHPUnit\Framework\TestCase
{

    public function testEscape()
    {
        $view = new View(TestApp::$di);
        $this->assertEquals((new EscapeFilter)->filter('test'), (new Escape($view))->escape('test'));
        $this->assertEquals((new EscapeFilter)->filter('<script>alert();</script>'), (new Escape($view))->escape('<script>alert();</script>'));
    }

}

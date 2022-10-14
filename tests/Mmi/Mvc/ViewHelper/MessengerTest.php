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
use Mmi\Mvc\View;

class MessengerTest extends \PHPUnit\Framework\TestCase
{

    public function testClass()
    {
        $_SESSION = [];
        $view = TestApp::$di->get(View::class);
        $view->getMessenger()->clearMessages();
        $this->assertEquals('', $view->messenger());
        $view->getMessenger()->addMessage('test');
        $this->assertEquals('<ul id="messenger" class="messenger">' . "\n" .
        '                                    <li class="notice warning"><i class="icon-warning-sign icon-large"></i><div class="alert">test<a class="close-alert" href="#"></a></div></li>' . "\n" .
        '    </ul>' . "\n", $view->messenger());
    }

}

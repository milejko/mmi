<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Tests;

use Mmi\App\TestApp;

/**
 * Test index controllera
 */
class IndexControllerTest extends \PHPUnit\Framework\TestCase
{
    public function testIndexAction()
    {
        $this->assertEquals(file_get_contents(BASE_PATH . '/src/Mmi/Resource/template/index/index.tpl'), (new \Mmi\Mvc\ActionHelper(TestApp::$di))->action((new \Mmi\Http\Request)
                    ->setModuleName('mmi')
                    ->setControllerName('index')
                    ->setActionName('index')));
    }

    public function testErrorAction()
    {
        $this->assertEquals(file_get_contents(BASE_PATH . '/src/Mmi/Resource/template/index/error.tpl'), (new \Mmi\Mvc\ActionHelper(TestApp::$di))->action((new \Mmi\Http\Request)
                    ->setModuleName('mmi')
                    ->setControllerName('index')
                    ->setActionName('error')));
    }
}

<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\App;

use Mmi\App\Kernel;

/**
 * Test standardowego bootstrapa
 */
class BootstrapTest extends \PHPUnit\Framework\TestCase
{

    public static function setUpBeforeClass()
    {
        require 'data/config-cache.php';
        require 'data/config-nodb.php';
        require 'data/config-session.php';
    }

    public function testBootstrap()
    {
        ob_start();
        \Mmi\App\FrontController::getInstance()->getEnvironment()->applicationLanguage = 'pl';
        (new Kernel('\Mmi\App\Bootstrap', 'DEFAULT'))->run();
        $this->assertEquals(145, strpos(ob_get_clean(), \Mmi\IndexController::DEFAULT_LABEL), 'Running bootstrap does not return default label from IndexController');
    }

    public function testBootstrapCache()
    {
        ob_start();
        \Mmi\App\FrontController::getInstance()->getEnvironment()->applicationLanguage = 'fr';
        (new Kernel('\Mmi\App\Bootstrap', 'CACHE'))->run();
        $this->assertEquals(145, strpos(ob_get_clean(), \Mmi\IndexController::DEFAULT_LABEL), 'Running bootstrap does not return default label from IndexController');
    }

    public function testBootstrapNoDb()
    {
        ob_start();
        (new Kernel('\Mmi\App\Bootstrap', 'NODB'))->run();
        $this->assertEquals(145, strpos(ob_get_clean(), \Mmi\IndexController::DEFAULT_LABEL), 'Running bootstrap does not return default label from IndexController');
    }

    public function testBootstrapSession()
    {
        ob_start();
        (new Kernel('\Mmi\App\Bootstrap', 'SESSION'))->run();
        $this->assertEquals(145, strpos(ob_get_clean(), \Mmi\IndexController::DEFAULT_LABEL), 'Running bootstrap does not return default label from IndexController');
    }

}

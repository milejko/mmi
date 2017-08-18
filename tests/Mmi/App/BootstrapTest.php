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

    public function testBootstrap()
    {
        ob_start();
        (new Kernel('\Mmi\App\Bootstrap', 'DEV'))->run();
        $this->assertEquals(\Mmi\IndexController::DEFAULT_LABEL, ob_get_clean());
    }

}

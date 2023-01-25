<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Mvc\ViewHelper;

use Mmi\App\AppTesting;
use Mmi\Mvc\View;
use Mmi\Mvc\ViewHelper\Template;

class TemplateTest extends \PHPUnit\Framework\TestCase
{
    public function testClass()
    {
        $tpl = new Template(new View(AppTesting::$di));
        $this->assertStringContainsString('/?module=test', $tpl->template('{@module=test@}'));
        $this->assertEquals(
            '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">' . "\n" .
            '<html><head>' . "\n" .
            '<title>It works!</title>' . "\n" .
            '</head><body>' . "\n" .
            '<h1>It works!</h1>' . "\n" .
            '<p>This is the default web page for this server.</p>' . "\n" .
            '<p>The web server software is running but no content has been added, yet.</p>' . "\n" .
            '</body></html>',
            $tpl->template('{\'mmi/index/index\'}')
        );
        $this->assertEquals('', $tpl->template('{\'inexistent/template\'}'));
        $this->assertEquals('<?php $this->test  =  1; ?><?php echo $this->test; ?>', $tpl->template('{$test = 1}{$test}'));
        $this->assertEquals('<?php $this->test  =  123; ?><?php echo $this->getFilter(\'truncate\')->setOptions(array(1))->filter($this->test); ?>', $tpl->template('{$test = 123}{$test|truncate:1}'));
        $this->assertEquals('test', $tpl->template('{#test#}'));
    }
}

<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Tests\Filter;

use Mmi\Filter\TinyMce;

class TinyMceTest extends \PHPUnit\Framework\TestCase
{

    public function testFilter()
    {
        $this->assertEquals('<div>test</div>', (new TinyMce)->filter('<div>test</div>'));
        $this->assertEquals('<div><p>test</p></div>', (new TinyMce)->filter('<div><p>test</p></div>'));
        $this->assertEquals('<div><p>test</p></div>', (new TinyMce)->filter('<div><p>test</p></div><script></script>'));
        $this->assertEquals('<div><p>test</p></div>', (new TinyMce)->filter('<div><p>test</p></div><head></head>'));
    }

}

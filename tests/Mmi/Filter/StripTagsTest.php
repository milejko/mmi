<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Filter;

use Mmi\Filter\StripTags;

class StripTagsTest extends \PHPUnit\Framework\TestCase
{

    public function testFilter()
    {
        $this->assertEquals('test', (new StripTags)->filter('<div>test</div>'));
        $this->assertEquals('<p>test</p>', (new StripTags(['<p>']))->filter('<div><p>test</p></div>'));
    }

}

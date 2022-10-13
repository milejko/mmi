<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Filter;

use Mmi\Filter\Urlencode;

class UppercaseTest extends \PHPUnit\Framework\TestCase
{

    public function testFilter()
    {
        $this->assertEquals('%C5%BC%C3%B3%C5%82ta+krowa+w+kropki+bordo%2C+gryz%C5%82a+traw%C4%99+kr%C4%99c%C4%85c+mord%C4%85...', (new Urlencode)->filter('żółta krowa w kropki bordo, gryzła trawę kręcąc mordą...'));
    }

}

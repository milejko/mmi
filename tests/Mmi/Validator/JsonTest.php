<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Validator;

use Mmi\Validator\Json;

class JsonTest extends \PHPUnit\Framework\TestCase
{

    public function testIsValid()
    {
        //równe
        $this->assertTrue((new Json)->isValid(json_encode(['test' => 'test'])));
        $this->assertTrue((new Json)->isValid(json_encode(new \stdClass())));
        $this->assertTrue((new Json)->isValid(json_encode([1, 2 , 3])));
        $this->assertTrue((new Json)->isValid('{}'));
        //nie równe
        $this->assertFalse((new Json)->isValid('{2: 2}'));
        $this->assertFalse((new Json)->isValid('{"2":"3""}'));
        $this->assertFalse((new Json)->isValid(null));
    }

}

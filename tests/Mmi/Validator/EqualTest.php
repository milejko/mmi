<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Validator;

class EqualTest extends \PHPUnit_Framework_TestCase {

	public function testValid() {
		$this->assertTrue((new Equal)->setValue(10)
				->isValid(10));
		$this->assertTrue((new Equal)->setValue(true)
				->isValid(1));
		$this->assertTrue((new Equal)->setValue(true)
				->isValid(true));
		$this->assertTrue((new Equal)->setValue(false)
				->isValid(0));
		$this->assertTrue((new Equal)->setValue(false)
				->isValid(false));
		$this->assertTrue((new Equal)->setValue(null)
				->isValid(null));
		$this->assertTrue((new Equal)->setValue(null)
				->isValid(false));
		$this->assertTrue((new Equal)->setValue(null)
				->isValid(''));
		$this->assertTrue((new Equal)->setValue('test')
				->isValid('test'));
		$this->assertTrue((new Equal)->setValue(new \stdClass())
				->isValid(new \stdClass()));
	}

	public function testInvalid() {
		$this->assertFalse((new Equal)->setValue(10)
				->isValid(9));
		$this->assertFalse((new Equal)->setValue(true)
				->isValid(false));
		$this->assertFalse((new Equal)->setValue('test')
				->isValid('test1'));
		$this->assertFalse((new Equal)->setValue(new \stdClass())
				->isValid(new Equal));
	}

}

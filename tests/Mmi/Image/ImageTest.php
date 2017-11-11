<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Image;

use Mmi\Image\Image;
/**
 * Test klasy obróbki obrazów
 */
class ImageTest extends \PHPUnit\Framework\TestCase
{

    CONST imagePath = BASE_PATH . '/tests/data/test.png';
    
    private $_img;
    private $_imagePath;
    
    public function setUp()
    {
        try {
            $this->_img = new Image();
            $this->_imagePath = self::imagePath;
        } catch(\Exception $ex) {
            return $ex->getMessage();
        }
    }
    
    
    public function testInputToResource()
    {
        $this->assertTrue(file_exists($this->_imagePath), 'No file');
        $this->assertEquals('resource', gettype($this->_img->inputToResource($this->_imagePath)));
        //jeśli resource zwrot
//        if (gettype($input) == 'resource') {
//            return $input;
//        }
        //jeśli krótki content zakłada że to ścieżka pliku
//        return imagecreatefromstring((strlen($input) < 1024) ? file_get_contents($input) : $input);
    }
    
    public function testScaleCrop()
    {
        $x = rand(1, 100);
        $y = rand(1, 100);
        $this->assertEquals('aaa', $this->_img->scale($this->_imagePath, $x, $y));
    }
    
    public function testScaleProportional()
    {
        $percent = rand(1, 100);
        $this->assertTrue($this->_img->scaleProportional($this->_imagePath, $percent), 'Obrazek przeskalowany o '.$percent.'%');
    }
    
    public function testScale()
    {
        
    }
    
    public function testScalex()
    {
        
    }
    
    public function testScaley()
    {
        
    }
    
    public function testScaleMax()
    {
        
    }
    
    public function testCrop()
    {
        
    }

    

}

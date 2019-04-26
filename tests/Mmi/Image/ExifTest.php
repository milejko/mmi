<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Image;

use Mmi\Image\Exif;
/**
 * Test klasy obróbki obrazów
 */
class ExifTest extends \PHPUnit\Framework\TestCase
{

    CONST imagePath1 = BASE_PATH . '/tests/data/test1.jpeg';
    CONST imagePath2 = BASE_PATH . '/tests/data/test2.jpeg';
    
    private $_img1;
    private $_img2;
    
    public function setUp(): void
    {
        try {
            $this->_img1 = new Exif(self::imagePath1);
            $this->_img2 = new Exif(self::imagePath2);
        } catch(\Exception $ex) {
            //$ex->getMessage();
        }
    }
    
    public function testGetIsoSpeed()
    {
        $this->assertEquals(null, $this->_img1->getIsoSpeed());
        $this->assertEquals('100', $this->_img2->getIsoSpeed());
    }

    public function testGetCamera()
    {
        $this->assertEquals('EASTMAN KODAK COMPANY', $this->_img1->getCamera());
        $this->assertEquals('Hasselblad', $this->_img2->getCamera());
    }
    
    public function testGetCameraMake()
    {
        $this->assertEquals('EASTMAN KODAK COMPANY', $this->_img1->getCameraMake());
        $this->assertEquals('Hasselblad', $this->_img2->getCameraMake());
    }
    
    public function testGetCameraModel()
    {
        $this->assertEquals(null, $this->_img1->getCameraModel());
        $this->assertEquals(null, $this->_img2->getCameraModel());
    }
    
    public function testGetExposureTime()
    {
        $this->assertEquals('1/8', $this->_img1->getExposureTime());
        $this->assertEquals('1/500', $this->_img2->getExposureTime());
    }
    
    public function testGetAperture()
    {
        $this->assertEquals('2.7999999999999998', $this->_img1->getAperture());
        $this->assertEquals('9.5', $this->_img2->getAperture());
    }

    public function testGetCreationDate()
    {
        $this->assertEquals('2004-04-10 20:38:21', $this->_img1->getCreationDate());
        $this->assertEquals('2016-10-07 11:51:32', $this->_img2->getCreationDate());
    }
    
    public function testGetWidth()
    {
        $this->assertEquals('1350.0', $this->_img1->getWidth());
        $this->assertEquals('8272.0', $this->_img2->getWidth());
    }
    
    public function testGetHeight()
    {
        $this->assertEquals('900.0', $this->_img1->getHeight());
        $this->assertEquals('6200.0', $this->_img2->getHeight());
    }
    
    public function testGetOrientation()
    {
        $this->assertEquals('1', $this->_img1->getOrientation());
        $this->assertEquals('1', $this->_img2->getOrientation());
    }
    
    public function testGetLongitude()
    {
        $this->assertEquals(null, $this->_img1->getLongitude());
        $this->assertEquals(null, $this->_img2->getLongitude());
    }
    
    public function testGetLatitude()
    {
        $this->assertEquals(null, $this->_img1->getLatitude());
        $this->assertEquals(null, $this->_img2->getLatitude());
    }
}

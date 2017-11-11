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

    CONST imagePath = BASE_PATH . '/tests/data/test2.jpeg';
    
    private $_img;
    
    public function setUp()
    {
        try {
            $this->_img = new Exif(self::imagePath);
        } catch(\Exception $ex) {
            return $ex->getMessage();
        }
    }
    
    public function testGetIsoSpeed()
    {
        $this->assertEquals(null, $this->_img->getIsoSpeed());
    }

    public function testGetCamera()
    {
        $this->assertEquals('EASTMAN KODAK COMPANY', $this->_img->getCamera());
    }
    
    public function testGetCameraMake()
    {
        $this->assertEquals('EASTMAN KODAK COMPANY', $this->_img->getCameraMake());
    }
    
    public function testGetCameraModel()
    {
        $this->assertEquals(null, $this->_img->getCameraModel());
    }
    
    public function testGetExposureTime()
    {
        $this->assertEquals('1/8', $this->_img->getExposureTime());
    }
    
    public function testGetAperture()
    {
        $this->assertEquals('2.7999999999999998', $this->_img->getAperture());
    }

    public function testGetCreationDate()
    {
        $this->assertEquals('2004-04-10 20:38:21', $this->_img->getCreationDate());
    }
    
    public function testGetWidth()
    {
        $this->assertEquals('1350.0', $this->_img->getWidth());
    }
    
    public function testGetHeight()
    {
        $this->assertEquals('900.0', $this->_img->getHeight());
    }
    
    public function testGetOrientation()
    {
        $this->assertEquals('1', $this->_img->getOrientation());
    }
    
    public function testGetLongitude()
    {
        $this->assertEquals(null, $this->_img->getLongitude());
    }
    
    public function testGetLatitude()
    {
        $this->assertEquals(null, $this->_img->getLatitude());
    }
}

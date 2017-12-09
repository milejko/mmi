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

    CONST IMAGE_PATH = BASE_PATH . 'tests/data/test.png';
    CONST VERTICAL_IMAGE_PATH = BASE_PATH . 'tests/data/test-vertical.png';
    CONST TMP_PATH = BASE_PATH . 'var/cache/test.png';
    CONST RESOURCE_TYPE = 'resource';

    public function testInputToResource()
    {
        //ze ścieżki do zasobu
        $this->assertEquals(self::RESOURCE_TYPE, gettype(Image::inputToResource(self::IMAGE_PATH)));
        //binary do zasobu
        $this->assertEquals(self::RESOURCE_TYPE, gettype(Image::inputToResource(file_get_contents(self::IMAGE_PATH))));
        //z zasobu do zasobu
        $this->assertEquals(self::RESOURCE_TYPE, gettype(Image::inputToResource(Image::inputToResource(self::IMAGE_PATH))));
        $this->assertEquals(360, imagesx(Image::inputToResource(self::VERTICAL_IMAGE_PATH)));
    }

    public function testScaleCrop()
    {
        //zmniejszenie
        $this->assertEquals(self::RESOURCE_TYPE, gettype($resource = Image::scaleCrop(self::IMAGE_PATH, 320, 200)));
        $this->assertEquals(320, imagesx($resource));
        $this->assertEquals(200, imagesy($resource));
        //png
        imagepng($resource, self::TMP_PATH);
        //rozmiar > 20k
        $this->assertGreaterThan(20000, filesize(self::TMP_PATH));
        //badanie miniatury
        $this->assertEquals(320, imagesx(Image::inputToResource(self::TMP_PATH)));
        $this->assertEquals(200, imagesy(Image::inputToResource(self::TMP_PATH)));
        unlink(self::TMP_PATH);
        //powiększenie
        $upscale = Image::scaleCrop(self::IMAGE_PATH, 1000, 900);
        $this->assertEquals(1000, imagesx($upscale));
        $this->assertEquals(900, imagesy($upscale));
        //png
        imagepng($upscale, self::TMP_PATH);
        //rozmiar > 20k
        $this->assertGreaterThan(50000, filesize(self::TMP_PATH));
        unlink(self::TMP_PATH);
        $this->assertEquals(144, imagesx(Image::scaleCrop(self::VERTICAL_IMAGE_PATH, 144, 144)));
        $this->assertEquals(72, imagesy(Image::scaleCrop(self::VERTICAL_IMAGE_PATH, 72, 72)));
    }

    public function testScaleProportional()
    {
        //100%
        $this->assertEquals(self::RESOURCE_TYPE, gettype($resource = Image::scaleProportional(self::IMAGE_PATH, 100)));
        $this->assertEquals(640, imagesx($resource));
        $this->assertEquals(360, imagesy($resource));
        //powiększenie
        $upscale = Image::scaleProportional(self::IMAGE_PATH, 200);
        $this->assertEquals(1280, imagesx($upscale));
        $this->assertEquals(720, imagesy($upscale));
        $this->assertEquals(396, imagesy(Image::scaleProportional(self::IMAGE_PATH, 110)));
        //pomniejszanie
        $this->assertEquals(320, imagesx(Image::scaleProportional(self::IMAGE_PATH, 50)));
        $this->assertEquals(36, imagesy(Image::scaleProportional(self::IMAGE_PATH, 10)));
    }

    public function testScale()
    {
        //100%
        $this->assertEquals(self::RESOURCE_TYPE, gettype($resource = Image::scale(self::IMAGE_PATH, 640)));
        $this->assertEquals(640, imagesx($resource));
        $this->assertEquals(360, imagesy($resource));
        $this->assertEquals(640, imagesx(Image::scale(self::IMAGE_PATH)));
        $this->assertEquals(360, imagesy(Image::scale(self::IMAGE_PATH)));
        //powiększenie
        $upscale = Image::scale(self::IMAGE_PATH, 1600, 1200);
        $this->assertEquals(1600, imagesx($upscale));
        $this->assertEquals(900, imagesy($upscale));
        $this->assertEquals(1000, imagesx(Image::scale(self::IMAGE_PATH, 1000, 1200)));
        $this->assertEquals(563, imagesy(Image::scale(self::IMAGE_PATH, 1000, 1000)));
        $this->assertEquals(1067, imagesy(Image::scale(self::VERTICAL_IMAGE_PATH, 600)));
        //pomniejszanie
        $this->assertEquals(320, imagesx(Image::scale(self::IMAGE_PATH, 320)));
        $this->assertEquals(36, imagesy(Image::scale(self::IMAGE_PATH, 1000, 36)));
        $this->assertEquals(320, imagesx(Image::scale(self::IMAGE_PATH, 320, 320)));
    }

    public function testScalex()
    {
        $this->assertEquals(self::RESOURCE_TYPE, gettype($resource = Image::scalex(self::IMAGE_PATH, 640)));
        $this->assertEquals(640, imagesx($resource));
        $this->assertEquals(360, imagesy($resource));
        $this->assertEquals(312, imagesx(Image::scalex(self::VERTICAL_IMAGE_PATH, 312)));
        $this->assertEquals(555, imagesy(Image::scalex(self::VERTICAL_IMAGE_PATH, 312)));
    }

    public function testScaley()
    {
        $this->assertEquals(self::RESOURCE_TYPE, gettype($resource = Image::scaley(self::IMAGE_PATH, 640)));
        $this->assertEquals(1138, imagesx($resource));
        $this->assertEquals(640, imagesy($resource));
        $this->assertEquals(312, imagesx(Image::scalex(self::VERTICAL_IMAGE_PATH, 312)));
        $this->assertEquals(555, imagesy(Image::scalex(self::VERTICAL_IMAGE_PATH, 312)));
    }
    
    public function testScaleMax() {
        $this->assertEquals(360, imagesx(Image::scaleMax(self::VERTICAL_IMAGE_PATH, 1024)));
        $this->assertEquals(113, imagesx(Image::scaleMax(self::VERTICAL_IMAGE_PATH, 200)));
        $this->assertEquals(56, imagesx(Image::scaleMax(self::VERTICAL_IMAGE_PATH, 100)));
        
    }

    public function testCrop()
    {
        $this->assertEquals(2, imagesx(Image::crop(self::IMAGE_PATH, 1, 1, 2, 2)));
        $this->assertEquals(639, imagesx(Image::crop(self::IMAGE_PATH, 1, 1, 2000, 2000)));
        $this->assertEquals(359, imagesy(Image::crop(self::IMAGE_PATH, 1, 1, 2000, 2000)));
    }

}

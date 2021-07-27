<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test;

use Mmi\FileSystem;

/**
 * Test klasy obsługi dysku
 */
class FileSystemTest extends \PHPUnit\Framework\TestCase
{

    public function testCopyRecursive()
    {
        //cleanup
        FileSystem::rmdirRecursive(BASE_PATH . '/var/data/tests');
        $this->assertFalse(FileSystem::copyRecursive(BASE_PATH . '/surely-non-existent', BASE_PATH . '/var/data/tests'), 'Able to copy inexistent files');
        $this->assertTrue(FileSystem::copyRecursive(BASE_PATH . '/tests', BASE_PATH . '/var/data/tests'), 'Unable to copy files');
        $this->assertTrue(file_exists(BASE_PATH . '/var/data/tests/Mmi/FileSystemTest.php'), 'File not found');
    }

    /**
     * @depends testCopyRecursive
     */
    public function testUnlinkRecursive()
    {
        //invalid path
        $this->assertFalse(FileSystem::unlinkRecursive('FileSystemTest.php', BASE_PATH . '/var/data/surely-non-existent'));
        $this->assertTrue(file_exists(BASE_PATH . '/var/data/tests/Mmi/FileSystemTest.php'), 'File not found');
        $this->assertTrue(FileSystem::unlinkRecursive('FileSystemTest.php', BASE_PATH . '/var/data/tests'));
        $this->assertFalse(file_exists(BASE_PATH . '/var/data/tests/Mmi/FileSystemTest.php'), 'File still found');
    }

    public function testRmdirRecursive()
    {
        $this->assertTrue(FileSystem::copyRecursive(BASE_PATH . '/tests', BASE_PATH . '/var/data/tests'), 'Unable to copy files');
        $this->assertTrue(file_exists(BASE_PATH . '/var/data/tests/Mmi/FileSystemTest.php'), 'File not found');
        $this->assertTrue(FileSystem::rmdirRecursive(BASE_PATH . '/var/data/tests'), 'Directory not found');
        $this->assertFalse(file_exists(BASE_PATH . '/var/data/tests'), 'Directory still found');
        $this->assertFalse(FileSystem::rmdirRecursive(BASE_PATH . '/var/data/tests'), 'Able to delete inexistent directory');
    }

    public function testMimeType()
    {
        $this->assertEquals('image/png', FileSystem::mimeType(BASE_PATH . '/tests/data/test.png'));
        $this->assertEquals('image/png', FileSystem::mimeTypeBinary(file_get_contents(BASE_PATH . '/tests/data/test.png')));
    }

}

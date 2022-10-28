<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Paginator;

use Mmi\Paginator\Paginator;

class PaginatorTest extends \PHPUnit\Framework\TestCase
{
    public function testPaginator()
    {
        $paginator = (new Paginator)
            ->setHashHref('test-href');
        $this->assertEquals(0, $paginator->getPagesCount());
        $this->assertInstanceOf('\Mmi\Paginator\Paginator', $paginator->setRowsPerPage(0));
        $this->assertEquals(0, $paginator->getPagesCount());
        $this->assertEquals('', (string) $paginator);

        $this->assertInstanceOf('\Mmi\Paginator\Paginator', $paginator->setRowsPerPage(11));
        $this->assertInstanceOf('\Mmi\Paginator\Paginator', $paginator->setRowsCount(34));
        $this->assertEquals(34, $paginator->getRowsCount());
        $this->assertEquals(4, $paginator->getPagesCount());

        $this->assertInstanceOf('\Mmi\Paginator\Paginator', $paginator->setRowsCount(7));
        $this->assertEquals(7, $paginator->getRowsCount());
        $this->assertEquals(1, $paginator->getPagesCount());
        //brak stron
        $this->assertEquals('', (string) $paginator);

        $this->assertInstanceOf('\Mmi\Paginator\Paginator', $paginator->setRowsCount(21));
        $this->assertEquals(21, $paginator->getRowsCount());
        $this->assertEquals(2, $paginator->getPagesCount());

        $renderedPaginator = (string) $paginator;

        $this->assertEquals(2, $paginator->setPage(2)
                ->getPage());

        $this->assertStringStartsNotWith('<div class="paginator">', $renderedPaginator);
    }
}

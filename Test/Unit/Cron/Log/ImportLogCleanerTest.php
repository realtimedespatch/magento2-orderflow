<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Cron\Log;

use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Cron\Log\ImportLogCleaner;
use RealtimeDespatch\OrderFlow\Helper\Log\Cleaning;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Import\Collection;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Import\CollectionFactory;


class ImportLogCleanerTest extends TestCase
{
    protected $helper;
    protected $collection;

    protected $cron;

    public function setUp()
    {
        $this->helper = $this->getMockBuilder(Cleaning::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->collection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $collectionFactory = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $collectionFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->collection);

        $this->cron = new ImportLogCleaner($collectionFactory, $this->helper);
    }

    public function testExecute()
    {
        $cutoff = '2020-11-30';

        $this->helper->expects($this->exactly(2))
            ->method('isEnabled')
            ->will($this->onConsecutiveCalls(false, true));

        $this->helper->expects($this->once())
            ->method('getImportLogExpirationDate')
            ->willReturn($cutoff);

        $this->collection->expects($this->once())
            ->method('deleteOlderThanCutoff')
            ->with($this->equalTo($cutoff));

        $this->assertFalse($this->cron->execute());
        $this->assertTrue($this->cron->execute());
    }
}

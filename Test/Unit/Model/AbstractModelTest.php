<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model;

use RealtimeDespatch\OrderFlow\Model\Export;

abstract class AbstractModelTest extends \PHPUnit\Framework\TestCase
{
    protected  \Magento\Framework\Model\Context $mockContext;
    protected \Magento\Framework\Registry $mockRegistry;
    protected \Magento\Framework\Model\ResourceModel\AbstractResource $mockResource;
    protected \Magento\Framework\Data\Collection\AbstractDb $mockResourceCollection;
    protected string|null $idFieldName = null;

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(\Magento\Framework\Model\Context::class);
        $this->mockRegistry = $this->createMock(\Magento\Framework\Registry::class);
        $this->mockResource = $this->getMockBuilder(\Magento\Framework\Model\ResourceModel\AbstractResource::class)
            ->disableOriginalConstructor()
            ->addMethods(['getIdFieldName'])
            ->onlyMethods(['_construct', 'getConnection'])
            ->getMock();
        $this->mockResourceCollection = $this->getMockBuilder(\Magento\Framework\Data\Collection\AbstractDb::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['addFieldToFilter', 'load', 'getResource'])
            ->addMethods(['addFieldToSelect'])
            ->getMock();
        $this->mockResource
            ->method('getIdFieldName')
            ->willReturn($this->idFieldName);
    }

    abstract public function testData(): void;
}
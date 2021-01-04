<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Import\View;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Api\Data\ImportInterface;
use RealtimeDespatch\OrderFlow\Api\ImportRepositoryInterface;
use RealtimeDespatch\OrderFlow\Block\Adminhtml\Import\View\InventoryInfo;

class InventoryInfoTest extends TestCase
{
    /**
     * @var InventoryInfo
     */
    protected $block;

    public function setUp()
    {
        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request = $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $importRepository = $this->getMockBuilder(ImportRepositoryInterface::class)
            ->getMock();

        $data = [];

        $this->block = new InventoryInfo(
            $context,
            $request,
            $importRepository,
            $data
        );
    }

    public function testToHtmlReturnsEmptyStringForLocalisedException()
    {
        $entity = 'Shipment';
        $expectedValue = '';

        $import = $this->getMockBuilder(ImportInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $import->expects($this->once())
            ->method('getEntity')
            ->willThrowException(new LocalizedException(__('Test')));

        $this->block->setImport($import);

        $this->assertEquals($expectedValue, $this->block->toHtml());
    }

    public function testToHtmlReturnsEmptyStringForNonInventoryImport()
    {
        $entity = 'Shipment';
        $expectedValue = '';

        $import = $this->getMockBuilder(ImportInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $import->expects($this->once())
            ->method('getEntity')
            ->willReturn($entity);

        $this->block->setImport($import);

        $this->assertEquals($expectedValue, $this->block->toHtml());
    }
}

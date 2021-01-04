<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Helper\Import;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Api\Data\ImportInterface;
use RealtimeDespatch\OrderFlow\Helper\Import\Shipment;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request\Collection;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request\CollectionFactory as RequestCollectionFactory;

class ShipmentTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var MockObject
     */
    protected $reqCollectionFactory;

    /**
     * @var Shipment
     */
    protected $helper;

    public function setUp()
    {
        $this->scopeConfigMock = $this->getMockBuilder(ScopeConfigInterface::class)
            ->getMock();

        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $context->expects($this->once())
            ->method('getScopeConfig')
            ->willReturn($this->scopeConfigMock);

        $this->reqCollectionFactory = $this->getMockBuilder(RequestCollectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->helper = new Shipment(
            $context,
            $this->reqCollectionFactory
        );
    }

    /**
     * @dataProvider dataProviderIsEnabled
     * @param $configPath
     * @param $configValue
     * @param $returnValue
     */
    public function testIsEnabled($configPath, $configValue, $returnValue)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('isSetFlag')
            ->with($configPath, ScopeInterface::SCOPE_WEBSITE)
            ->willReturn($configValue);

        $this->assertEquals($returnValue, $this->helper->isEnabled());
    }

    /**
     * @return array
     */
    public function dataProviderIsEnabled()
    {
        return [
            ['orderflow_shipment_import/settings/is_enabled', true, true],
            ['orderflow_shipment_import/settings/is_enabled', false, false],
        ];
    }

    /**
     * @dataProvider dataProviderGetBatchSize
     * @param $configPath
     * @param $configValue
     * @param $returnValue
     */
    public function testGetBatchSize($configPath, $configValue, $returnValue)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with($configPath, ScopeInterface::SCOPE_WEBSITE)
            ->willReturn($configValue);

        $this->assertEquals($returnValue, $this->helper->getBatchSize());
    }

    /**
     * @return array
     */
    public function dataProviderGetBatchSize()
    {
        return [
            ['orderflow_shipment_import/settings/batch_size', null, 0],
            ['orderflow_shipment_import/settings/batch_size', false, 0],
            ['orderflow_shipment_import/settings/batch_size', 10, 10],
        ];
    }

    /**
     * @depends testGetBatchSize
     */
    public function testGetImportableRequests()
    {
        $batchSize = 10;
        $importableRequests = [];

        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with('orderflow_shipment_import/settings/batch_size', ScopeInterface::SCOPE_WEBSITE)
            ->willReturn($batchSize);

        $reqCollection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $reqCollection->expects($this->once())
            ->method('getImportableRequests')
            ->with(ImportInterface::ENTITY_SHIPMENT, $batchSize)
            ->willReturn($importableRequests);

        $this->reqCollectionFactory->expects($this->once())
            ->method('create')
            ->willReturn($reqCollection);

        $this->assertSame($importableRequests, $this->helper->getImportableRequests());
    }
}

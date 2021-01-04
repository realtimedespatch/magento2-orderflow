<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Helper\Import;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Api\Data\ImportInterface;
use RealtimeDespatch\OrderFlow\Helper\Import\Inventory;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request\Collection;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request\CollectionFactory as RequestCollectionFactory;

class InventoryTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var MockObject
     */
    protected $dateTime;

    /**
     * @var MockObject
     */
    protected $reqCollectionFactory;

    /**
     * @var Inventory
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

        $this->dateTime = $this->getMockBuilder(DateTime::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->reqCollectionFactory = $this->getMockBuilder(RequestCollectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->helper = new Inventory(
            $context,
            $this->dateTime,
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
            ['orderflow_inventory_import/settings/is_enabled', true, true],
            ['orderflow_inventory_import/settings/is_enabled', false, false],
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
            ['orderflow_inventory_import/settings/batch_size', null, 0],
            ['orderflow_inventory_import/settings/batch_size', false, 0],
            ['orderflow_inventory_import/settings/batch_size', 10, 10],
        ];
    }

    /**
     * @dataProvider dataProviderIsNegativeQuantityEnabled
     * @param $configPath
     * @param $configValue
     * @param $returnValue
     */
    public function testIsNegativeQuantityEnabled($configPath, $configValue, $returnValue)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('isSetFlag')
            ->with($configPath, ScopeInterface::SCOPE_WEBSITE)
            ->willReturn($configValue);

        $this->assertEquals($returnValue, $this->helper->isNegativeQtyEnabled());
    }

    /**
     * @return array
     */
    public function dataProviderIsNegativeQuantityEnabled()
    {
        return [
            ['orderflow_inventory_import/settings/negative_qtys_enabled', true, true],
            ['orderflow_inventory_import/settings/negative_qtys_enabled', false, false],
        ];
    }

    /**
     * @dataProvider dataProviderIsInventoryAdjustmentEnabled
     * @param $configPath
     * @param $configValue
     * @param $returnValue
     */
    public function testIsInventoryAdjustmentEnabled($configPath, $configValue, $returnValue)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('isSetFlag')
            ->with($configPath, ScopeInterface::SCOPE_WEBSITE)
            ->willReturn($configValue);

        $this->assertEquals($returnValue, $this->helper->isInventoryAdjustmentEnabled());
    }

    /**
     * @return array
     */
    public function dataProviderIsInventoryAdjustmentEnabled()
    {
        return [
            ['orderflow_inventory_import/settings/adjust_inventory', true, true],
            ['orderflow_inventory_import/settings/adjust_inventory', false, false],
        ];
    }

    /**
     * @dataProvider dataProviderIsUnsentOrderAdjustmentEnabled
     * @param $configPath
     * @param $configValue
     * @param $returnValue
     */
    public function testIsUnsentOrderAdjustmentEnabled($configPath, $configValue, $returnValue)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('isSetFlag')
            ->with($configPath, ScopeInterface::SCOPE_WEBSITE)
            ->willReturn($configValue);

        $this->assertEquals($returnValue, $this->helper->isUnsentOrderAdjustmentEnabled());
    }

    /**
     * @return array
     */
    public function dataProviderIsUnsentOrderAdjustmentEnabled()
    {
        return [
            ['orderflow_inventory_import/settings/adjust_inventory', true, true],
            ['orderflow_inventory_import/settings/adjust_inventory', false, false],
        ];
    }

    /**
     * @dataProvider dataProviderIsActiveQuoteAdjustmentEnabled
     * @param $configPath
     * @param $configValue
     * @param $returnValue
     */
    public function testIsActiveQuoteAdjustmentEnabled($configPath, $configValue, $returnValue)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('isSetFlag')
            ->with($configPath, ScopeInterface::SCOPE_WEBSITE)
            ->willReturn($configValue);

        $this->assertEquals($returnValue, $this->helper->isActiveQuoteAdjustmentEnabled());
    }

    /**
     * @return array
     */
    public function dataProviderIsActiveQuoteAdjustmentEnabled()
    {
        return [
            ['orderflow_inventory_import/settings/adjust_inventory', true, true],
            ['orderflow_inventory_import/settings/adjust_inventory', false, false],
        ];
    }

    /**
     * @dataProvider dataProviderGetValidUnsentOrderStatuses
     * @param $configPath
     * @param $configValue
     * @param $returnValue
     */
    public function testGetValidUnsentOrderStatuses($configPath, $configValue, $returnValue)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with($configPath, ScopeInterface::SCOPE_WEBSITE)
            ->willReturn($configValue);

        $this->assertEquals($returnValue, $this->helper->getValidUnsentOrderStatuses());
    }

    /**
     * @return array
     */
    public function dataProviderGetValidUnsentOrderStatuses()
    {
        return [
            [
                'orderflow_inventory_import/settings/valid_unsent_order_statuses',
                null,
                []
            ],
            [
                'orderflow_inventory_import/settings/valid_unsent_order_statuses',
                'pending,queued,exported',
                ['pending', 'queued', 'exported']
            ],
        ];
    }

    /**
     * @dataProvider dataProviderGetActiveQuoteCutoff
     * @param $configPath
     * @param $configValue
     * @param $returnValue
     */
    public function testGetActiveQuoteCutoff($configPath, $configValue, $returnValue)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with($configPath, ScopeInterface::SCOPE_WEBSITE)
            ->willReturn($configValue);

        $this->assertEquals($returnValue, $this->helper->getActiveQuoteCutoff());
    }

    /**
     * @return array
     */
    public function dataProviderGetActiveQuoteCutoff()
    {
        return [
            ['orderflow_inventory_import/settings/active_quote_cutoff', null, 0],
            ['orderflow_inventory_import/settings/active_quote_cutoff', false, 0],
            ['orderflow_inventory_import/settings/active_quote_cutoff', 10, 10],
        ];
    }

    /**
     * @dataProvider dataProviderGetActiveQuoteCutoffDate
     * @param $configPath
     * @param $cutOffInDays
     * @param $returnValue
     */
    public function testGetActiveQuoteCutoffDate($configPath, $cutOffInDays, $returnValue)
    {
        $dateFormat = 'Y-m-d H:i:s';
        $dateInput = '-'.$cutOffInDays.' days';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with($configPath)
            ->willReturn($cutOffInDays);

        $this->dateTime->expects($this->once())
            ->method('date')
            ->with($dateFormat, $dateInput)
            ->willReturn($returnValue);

        $this->assertEquals($returnValue, $this->helper->getActiveQuoteCutoffDate());
    }

    /**
     * @return array
     */
    public function dataProviderGetActiveQuoteCutoffDate()
    {
        return [
            ['orderflow_inventory_import/settings/active_quote_cutoff', 10, '2020-11-21 10:00:00'],
        ];
    }

    /**
     * @dataProvider dataProviderGetUnsentOrderCutoff
     * @param $configPath
     * @param $configValue
     * @param $returnValue
     */
    public function testGetUnsentOrderCutoff($configPath, $configValue, $returnValue)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with($configPath, ScopeInterface::SCOPE_WEBSITE)
            ->willReturn($configValue);

        $this->assertEquals($returnValue, $this->helper->getUnsentOrderCutoff());
    }

    /**
     * @return array
     */
    public function dataProviderGetUnsentOrderCutoff()
    {
        return [
            ['orderflow_inventory_import/settings/unsent_order_cutoff', null, 0],
            ['orderflow_inventory_import/settings/unsent_order_cutoff', false, 0],
            ['orderflow_inventory_import/settings/unsent_order_cutoff', 10, 10],
        ];
    }

    /**
     * @dataProvider dataProviderGetUnsentOrderCutoffDate
     * @param $configPath
     * @param $cutOffInDays
     * @param $returnValue
     */
    public function testGetUnsentOrderCutoffDate($configPath, $cutOffInDays, $returnValue)
    {
        $dateFormat = 'Y-m-d H:i:s';
        $dateInput = '-'.$cutOffInDays.' days';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with($configPath)
            ->willReturn($cutOffInDays);

        $this->dateTime->expects($this->once())
            ->method('date')
            ->with($dateFormat, $dateInput)
            ->willReturn($returnValue);

        $this->assertEquals($returnValue, $this->helper->getUnsentOrderCutoffDate());
    }

    /**
     * @return array
     */
    public function dataProviderGetUnsentOrderCutoffDate()
    {
        return [
            ['orderflow_inventory_import/settings/unsent_order_cutoff', 10, '2020-11-21 10:00:00'],
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
            ->with('orderflow_inventory_import/settings/batch_size', ScopeInterface::SCOPE_WEBSITE)
            ->willReturn($batchSize);

        $reqCollection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $reqCollection->expects($this->once())
            ->method('getImportableRequests')
            ->with(ImportInterface::ENTITY_INVENTORY, $batchSize)
            ->willReturn($importableRequests);

        $this->reqCollectionFactory->expects($this->once())
            ->method('create')
            ->willReturn($reqCollection);

        $this->assertSame($importableRequests, $this->helper->getImportableRequests());
    }
}

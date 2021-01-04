<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Helper\Export;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Website;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use RealtimeDespatch\OrderFlow\Helper\Export\Order;
use RealtimeDespatch\OrderFlow\Model\Source\Export\Status as ExportStatus;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request\CollectionFactory as RequestCollectionFactory;

class OrderTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var RequestCollectionFactory
     */
    protected $reqCollectionFactory;

    /**
     * @var Order
     */
    protected $helper;

    public function setUp()
    {
        $this->scopeConfigMock = $this->getMockBuilder(ScopeConfigInterface::class)
            ->getMock();

        $this->orderCollectionFactory = $this->getMockBuilder(OrderCollectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->reqCollectionFactory = $this->getMockBuilder(RequestCollectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $context->expects($this->once())
            ->method('getScopeConfig')
            ->willReturn($this->scopeConfigMock);

        $this->helper = new Order($context, $this->orderCollectionFactory, $this->reqCollectionFactory);
    }

    /**
     * @dataProvider dataProviderIsEnabled
     * @param $configPath
     * @param $configValue
     * @param $returnValue
     * @param $scopeId
     */
    public function testIsEnabled($configPath, $configValue, $returnValue, $scopeId)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('isSetFlag')
            ->with($configPath, ScopeInterface::SCOPE_WEBSITE, $scopeId)
            ->willReturn($configValue);

        $this->assertEquals($returnValue, $this->helper->isEnabled($scopeId));
    }

    /**
     * @return array
     */
    public function dataProviderIsEnabled()
    {
        return [
            ['orderflow_order_export/settings/is_enabled', true, true, 1],
            ['orderflow_order_export/settings/is_enabled', false, false, 1],
        ];
    }

    /**
     * @dataProvider dataProviderGetBatchSize
     * @param $configPath
     * @param $configValue
     * @param $scopeId
     * @param $returnValue
     */
    public function testGetBatchSize($configPath, $configValue, $scopeId, $returnValue)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with($configPath, ScopeInterface::SCOPE_WEBSITE)
            ->willReturn($configValue);

        $this->assertEquals($returnValue, $this->helper->getBatchSize($scopeId));
    }

    /**
     * @return array
     */
    public function dataProviderGetBatchSize()
    {
        return [
            ['orderflow_order_export/settings/batch_size', null, 1, 0],
            ['orderflow_order_export/settings/batch_size', false, 1, 0],
            ['orderflow_order_export/settings/batch_size', 10, 1, 10],
        ];
    }

    /**
     * @dataProvider dataProviderGetExportableOrderStatuses
     * @param $configPath
     * @param $configValue
     * @param $scopeId
     * @param $returnValue
     */
    public function testGetExportableOrderStatuses($configPath, $configValue, $scopeId, $returnValue)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with($configPath, ScopeInterface::SCOPE_WEBSITE)
            ->willReturn($configValue);

        $this->assertEquals($returnValue, $this->helper->getExportableOrderStatuses());
    }

    /**
     * @return array
     */
    public function dataProviderGetExportableOrderStatuses()
    {
        return [
            [
                'orderflow_order_export/settings/exportable_status',
                null,
                1,
                []
            ],
            [
                'orderflow_order_export/settings/exportable_status',
                'pending,queued,exported',
                1,
                ['pending', 'queued', 'exported']
            ],
        ];
    }

    /**
     * @dataProvider dataProviderCanExport
     * @param $orderState
     * @param $exportStatus
     * @param $validOrderStatuses
     * @param $scopeId
     * @param $expectedResult
     * @depends testGetExportableOrderStatuses
     */
    public function testCanExport(
        $orderState,
        $exportStatus,
        $validOrderStatuses,
        $scopeId,
        $expectedResult
    ) {
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with(
                'orderflow_order_export/settings/exportable_status',
                ScopeInterface::SCOPE_WEBSITE,
                $scopeId
            )
            ->willReturn($validOrderStatuses);


    }

    /**
     * @return array
     */
    public function dataProviderCanExport()
    {
        return [
            [
                \Magento\Sales\Model\Order::STATE_NEW,
                ExportStatus::STATUS_PENDING,
                [\Magento\Sales\Model\Order::STATE_PROCESSING, \Magento\Sales\Model\Order::STATE_COMPLETE],
                1,
                false
            ],
            [
                \Magento\Sales\Model\Order::STATE_PROCESSING,
                false,
                [\Magento\Sales\Model\Order::STATE_PROCESSING, \Magento\Sales\Model\Order::STATE_COMPLETE],
                1,
                false
            ],
            [
                \Magento\Sales\Model\Order::STATE_PROCESSING,
                ExportStatus::STATUS_QUEUED,
                [\Magento\Sales\Model\Order::STATE_PROCESSING, \Magento\Sales\Model\Order::STATE_COMPLETE],
                1,
                false
            ],
            [
                \Magento\Sales\Model\Order::STATE_PROCESSING,
                ExportStatus::STATUS_PENDING,
                [],
                1,
                false
            ],
            [
                \Magento\Sales\Model\Order::STATE_PROCESSING,
                ExportStatus::STATUS_PENDING,
                [\Magento\Sales\Model\Order::STATE_PROCESSING, \Magento\Sales\Model\Order::STATE_COMPLETE],
                1,
                true
            ],
        ];
    }

    /**
     * @depends testGetBatchSize
     * @depends testGetExportableOrderStatuses
     */
    public function testGetCreateableOrders()
    {
        $storeIds = ['1','2'];
        $websiteId = 1;
        $exportableOrderStatuses = 'processing,complete';
        $exportableOrderStatusesArray = ['processing' ,'complete'];
        $batchSize = 10;
        $pageNum = 1;

        $this->scopeConfigMock->expects($this->exactly(2))
            ->method('getValue')
            ->withConsecutive(
                ['orderflow_order_export/settings/exportable_status'],
                ['orderflow_order_export/settings/batch_size']
            )
            ->will($this->onConsecutiveCalls($exportableOrderStatuses, $batchSize));

        $website = $this->getMockBuilder(Website::class)
            ->disableOriginalConstructor()
            ->getMock();

        $website->expects($this->once())
            ->method('getStoreIds')
            ->willReturn($storeIds);

        $website->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($websiteId);

        $orderCollection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $orderCollection->expects($this->exactly(5))
            ->method('addFieldToFilter')
            ->withConsecutive(
                ['store_id', ['in' => $storeIds]],
                ['status', ['in' => $exportableOrderStatusesArray]],
                ['is_virtual', ['eq' => 0]],
                ['orderflow_export_date', ['null' => true]],
                [
                    'orderflow_export_status',
                    [
                        ['neq' => ExportStatus::STATUS_QUEUED],
                        ['null' => true],
                    ]
                ]
            )
            ->willReturn($orderCollection);

        $orderCollection->expects($this->once())
            ->method('setPage')
            ->with($pageNum, $batchSize)
            ->willReturn($orderCollection);

        $this->orderCollectionFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($orderCollection);

        $this->assertSame($orderCollection, $this->helper->getCreateableOrders($website));
    }
}

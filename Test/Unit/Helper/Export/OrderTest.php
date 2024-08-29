<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Helper\Export;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Model\OrderFactory;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Helper\Export\Order;


class OrderTest extends TestCase
{
    protected Order $orderHelper;
    protected ScopeConfigInterface $mockScopeConfig;
    protected OrderFactory $mockOrderFactory;
    protected TimezoneInterface $mockTimezone;

    protected function setUp(): void
    {
        $mockContext = $this->createMock(\Magento\Framework\App\Helper\Context::class);

        $this->mockScopeConfig = $this->createMock(\Magento\Framework\App\Config\ScopeConfigInterface::class);
        $mockContext->method('getScopeConfig')->willReturn($this->mockScopeConfig);

        $this->mockOrderFactory = $this->createMock(\Magento\Sales\Model\OrderFactory::class);
        $this->mockTimezone = $this->createMock(\Magento\Framework\Stdlib\DateTime\TimezoneInterface::class);

        $this->orderHelper = new Order(
            $mockContext,
            $this->mockOrderFactory,
            $this->mockTimezone
        );

        parent::setUp();
    }

    /**
     * @dataProvider boolDataProvider
     */
    public function testIsEnabled($enabled): void
    {
        $this->mockScopeConfig
            ->method('isSetFlag')
            ->with('orderflow_order_export/settings/is_enabled')
            ->willReturn($enabled);

        $this->assertEquals($enabled, $this->orderHelper->isEnabled());
    }

    public function testGetBatchSize(): void
    {
        $batchSize = rand(1, 100);

        $this->mockScopeConfig
            ->method('getValue')
            ->with('orderflow_order_export/settings/batch_size')
            ->willReturn($batchSize);

        $this->assertEquals($batchSize, $this->orderHelper->getBatchSize());
    }

    public function testGetExportableOrderStatuses(): void
    {
        $this->mockScopeConfig
            ->method('getValue')
            ->with('orderflow_order_export/settings/exportable_status')
            ->willReturn('processing,pending');

        $this->assertEquals(['processing', 'pending'], $this->orderHelper->getExportableOrderStatuses());
    }

    public function testGetCreateableOrders(): void
    {
        $mockOrder = $this->createMock(\Magento\Sales\Model\Order::class);

        $batchSize = rand(50, 100);

        $this->mockScopeConfig
            ->method('getValue')
            ->withConsecutive(
                ['orderflow_order_export/settings/exportable_status'],
                ['orderflow_order_export/settings/batch_size']
            )->willReturnOnConsecutiveCalls(
                'processing,pending',
                $batchSize,
            );

        $this->mockOrderFactory
            ->method('create')
            ->willReturn($mockOrder);

        $mockOrderCollection = $this->createMock(\Magento\Sales\Model\ResourceModel\Order\Collection::class);
        $mockOrderCollection
            ->method('addFieldToFilter')
            ->willReturnSelf();

        $mockOrderCollection
            ->expects($this->once())
            ->method('setPage')
            ->with(1, $batchSize)
            ->willReturnSelf();

        $mockOrder
            ->expects($this->once())
            ->method('getCollection')
            ->willReturn($mockOrderCollection);

        $mockWebsite = $this->createMock(\Magento\Store\Model\Website::class);

        $this->assertInstanceOf(
            Collection::class,
            $this->orderHelper->getCreateableOrders($mockWebsite)
        );
    }

    public function boolDataProvider(): array
    {
        return [
            [true],
            [false],
        ];
    }
}
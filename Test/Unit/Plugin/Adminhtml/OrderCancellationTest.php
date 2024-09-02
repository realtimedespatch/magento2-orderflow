<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Plugin\Adminhtml;

use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderRepository;
use Magento\Store\Model\Store;
use Magento\Store\Model\Website;
use RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface;
use RealtimeDespatch\OrderFlow\Model\Builder\RequestBuilder;
use RealtimeDespatch\OrderFlow\Model\Request;
use RealtimeDespatch\OrderFlow\Model\Service\Request\RequestProcessor;
use RealtimeDespatch\OrderFlow\Plugin\Adminhtml\OrderCancellation;
use RealtimeDespatch\OrderFlow\Helper\Export\Order as OrderExportHelper;

class OrderCancellationTest extends \PHPUnit\Framework\TestCase
{
    protected OrderCancellation $plugin;
    protected OrderExportHelper $mockOrderExportHelper;
    protected RequestBuilderInterface $mockRequestBuilder;
    protected OrderRepository $mockOrderRepository;
    protected OrderInterface $mockOrder;
    protected ObjectManagerInterface $mockObjectManager;
    protected RequestProcessor $mockRequestProcessor;
    protected Request $mockRequest;
    protected Store $mockStore;
    protected Website $mockWebsite;

    protected function setUp(): void
    {
        $this->mockOrderExportHelper = $this->createMock(OrderExportHelper::class);
        $this->mockRequestBuilder = $this->createMock(RequestBuilder::class);
        $this->mockOrderRepository = $this->createMock(OrderRepository::class);
        $this->mockObjectManager = $this->createMock(ObjectManagerInterface::class);
        $this->mockRequestProcessor = $this->createMock(RequestProcessor::class);
        $this->mockRequest = $this->createMock(Request::class);
        $this->mockStore = $this->createMock(Store::class);
        $this->mockWebsite = $this->createMock(Website::class);
        $this->mockOrder = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['canCancel', 'getIncrementId', 'getId', 'getStore'])
            ->addMethods(['getOrderflowExportStatus'])
            ->getMock();

        $this->mockStore
            ->method('getWebsite')
            ->willReturn($this->mockWebsite);

        $this->mockOrder
            ->method('getStore')
            ->willReturn($this->mockStore);

        $this->plugin = new OrderCancellation(
            $this->mockOrderExportHelper,
            $this->mockRequestBuilder,
            $this->mockOrderRepository,
            $this->mockObjectManager
        );
    }

    public function testBeforeCancelDisabled(): void
    {
        $this->mockOrderExportHelper
            ->method('isEnabled')
            ->willReturn(false);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Order exports are currently disabled. Please review the OrderFlow module configuration.');
        $this->plugin->beforeCancel($this->mockOrder);
    }

    public function testBeforeCancelCantCancel(): void
    {
        $this->mockOrderExportHelper
            ->method('isEnabled')
            ->willReturn(true);

        $this->mockOrder
            ->expects($this->once())
            ->method('canCancel')
            ->willReturn(false);

        $this->mockOrder
            ->expects($this->never())
            ->method('getOrderflowExportStatus');

        $this->plugin->beforeCancel($this->mockOrder);
    }

    public function testBeforeCancelQueued(): void
    {
        $this->mockOrderExportHelper
            ->method('isEnabled')
            ->willReturn(true);

        $this->mockOrder
            ->expects($this->once())
            ->method('canCancel')
            ->willReturn(true);

        $this->mockOrder
            ->expects($this->any())
            ->method('getOrderflowExportStatus')
            ->willReturn('Queued');

        $this->mockOrder
            ->expects($this->once())
            ->method('getStore')
            ->willReturn($this->mockStore);

       $this->expectException(\Exception::class);
       $this->expectExceptionMessage('Cannot cancel an order awaiting export to OrderFlow.');

        $this->mockRequestBuilder
            ->expects($this->never())
            ->method('setRequestData');

        $this->plugin->beforeCancel($this->mockOrder);
    }

    public function testBeforeCancel(): void
    {
        $this->mockOrderExportHelper
            ->method('isEnabled')
            ->willReturn(true);

        $this->mockOrder
            ->expects($this->once())
            ->method('canCancel')
            ->willReturn(true);

        $this->mockOrder
            ->expects($this->any())
            ->method('getOrderflowExportStatus')
            ->willReturnOnConsecutiveCalls(
                'Exported',
                'Cancelled'
            );

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('setRequestData')
            ->with(
                'Export',
                'Order',
                'Cancel'
            );

        $incrementId = '100000001';

        $this->mockOrder
            ->expects($this->once())
            ->method('getIncrementId')
            ->willReturn($incrementId);

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('addRequestLine')
            ->with(json_encode(['increment_id' => $incrementId]));

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('saveRequest')
            ->willReturn($this->mockRequest);

        $this->mockObjectManager
            ->expects($this->once())
            ->method('create')
            ->with('OrderCancelRequestProcessor')
            ->willReturn($this->mockRequestProcessor);

        $this->mockOrder
            ->method('getId')
            ->willReturn(1);

        $this->mockOrderRepository
            ->expects($this->once())
            ->method('get')
            ->with(1)
            ->willReturn($this->mockOrder);

        $this->plugin->beforeCancel($this->mockOrder);
    }
}
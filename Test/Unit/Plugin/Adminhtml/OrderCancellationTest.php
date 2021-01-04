<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Plugin\Adminhtml;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Helper\Export\Order as OrderHelper;
use RealtimeDespatch\OrderFlow\Model\Service\Request\RequestProcessor;
use RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use RealtimeDespatch\OrderFlow\Plugin\Adminhtml\OrderCancellation;
use Magento\Sales\Model\Order;
use RealtimeDespatch\OrderFlow\Model\Source\Export\Status as ExportStatus;

class OrderCancellationTest extends TestCase
{
    /**
     * @var MockObject|OrderHelper
     */
    protected $helper;

    /**
     * @var MockObject|RequestProcessor
     */
    protected $requestProcessor;

    /**
     * @var MockObject|RequestBuilderInterface
     */
    protected $requestBuilder;

    /**
     * @var OrderRepositoryInterface|MockObject
     */
    protected $orderRepository;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @var OrderCancellation
     */
    protected $plugin;

    public function setUp()
    {
        $this->helper = $this->getMockBuilder(OrderHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestProcessor = $this->getMockBuilder(RequestProcessor::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestBuilder = $this->getMockBuilder(RequestBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->orderRepository = $this->getMockBuilder(OrderRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->order = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->plugin = new OrderCancellation(
            $this->helper,
            $this->requestProcessor,
            $this->requestBuilder,
            $this->orderRepository
        );
    }

    public function testBeforeCancelWhenOrderCancellationsAreDisabled()
    {
        $isEnabled = false;

        $this->helper->expects($this->once())
            ->method('isEnabled')
            ->willReturn($isEnabled);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Order Exports are Disabled. Please review the OrderFlow module configuration.');

        $this->plugin->beforeCancel($this->order);
    }

    public function testBeforeCancelWhenOrderIsNotCancellable()
    {
        $isEnabled = true;
        $isOrderCancellable= false;

        $this->helper->expects($this->once())
            ->method('isEnabled')
            ->willReturn($isEnabled);

        $this->order->expects($this->once())
            ->method('canCancel')
            ->willReturn($isOrderCancellable);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Order cancellation failed - the order cannot be cancelled.');

        $this->plugin->beforeCancel($this->order);
    }

    public function testBeforeCancelWhenOrderIsPendingExport()
    {
        $isEnabled = true;
        $isOrderCancellable= true;
        $exportStatus = ExportStatus::STATUS_QUEUED;

        $this->helper->expects($this->once())
            ->method('isEnabled')
            ->willReturn($isEnabled);

        $this->order->expects($this->once())
            ->method('canCancel')
            ->willReturn($isOrderCancellable);

        $this->order->expects($this->once())
            ->method('getData')
            ->with($this->equalTo('orderflow_export_status'))
            ->willReturn($exportStatus);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Order cancellation failed - the order is already queued for export.');

        $this->plugin->beforeCancel($this->order);
    }

    public function testBeforeCancelOnRequestException()
    {
        $orderId = 666;
        $incrementId = 666666666;
        $isEnabled = true;
        $isOrderCancellable= true;

        $this->helper->expects($this->once())
            ->method('isEnabled')
            ->willReturn($isEnabled);

        $this->order->expects($this->once())
            ->method('canCancel')
            ->willReturn($isOrderCancellable);

        $this->order->expects($this->exactly(1))
            ->method('getData')
            ->with($this->equalTo('orderflow_export_status'))
            ->will($this->onConsecutiveCalls(ExportStatus::STATUS_PENDING));

        $this->order->expects($this->never())
            ->method('getId')
            ->willReturn($orderId);

        $this->order->expects($this->once())
            ->method('getIncrementId')
            ->willReturn($incrementId);

        $this->orderRepository->expects($this->never())
            ->method('get')
            ->with($this->equalTo($orderId))
            ->willReturn($this->order);

        $this->requestBuilder->expects($this->once())
            ->method('saveRequest')
            ->with(
                RequestInterface::TYPE_EXPORT,
                RequestInterface::ENTITY_ORDER,
                RequestInterface::OP_CANCEL,
                null,
                [json_encode(['increment_id' => $incrementId])]
            )
            ->willThrowException(new CouldNotSaveException(__('Exception')));

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Order cancellation failed - please try again.');

        $this->plugin->beforeCancel($this->order);
    }

    public function testBeforeCancelWhenOrderCancellationFailed()
    {
        $orderId = 666;
        $incrementId = 666666666;
        $isEnabled = true;
        $isOrderCancellable= true;

        $this->helper->expects($this->once())
            ->method('isEnabled')
            ->willReturn($isEnabled);

        $this->order->expects($this->once())
            ->method('canCancel')
            ->willReturn($isOrderCancellable);

        $this->order->expects($this->exactly(2))
            ->method('getData')
            ->with($this->equalTo('orderflow_export_status'))
            ->will($this->onConsecutiveCalls(ExportStatus::STATUS_PENDING, ExportStatus::STATUS_FAILED));

        $this->order->expects($this->once())
            ->method('getId')
            ->willReturn($orderId);

        $this->order->expects($this->once())
            ->method('getIncrementId')
            ->willReturn($incrementId);

        $this->orderRepository->expects($this->once())
            ->method('get')
            ->with($this->equalTo($orderId))
            ->willReturn($this->order);

        $request = $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestBuilder->expects($this->once())
            ->method('saveRequest')
            ->with(
                RequestInterface::TYPE_EXPORT,
                RequestInterface::ENTITY_ORDER,
                RequestInterface::OP_CANCEL,
                null,
                [json_encode(['increment_id' => $incrementId])]
            )
            ->willReturn($request);

        $this->requestProcessor->expects($this->once())
            ->method('process')
            ->with($request);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Order cancellation failed - please try again.');

        $this->plugin->beforeCancel($this->order);
    }
}

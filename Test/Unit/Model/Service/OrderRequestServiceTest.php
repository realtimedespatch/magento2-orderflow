<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Service;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use RealtimeDespatch\OrderFlow\Model\Builder\RequestBuilder;
use RealtimeDespatch\OrderFlow\Model\Service\OrderRequestService;

class OrderRequestServiceTest extends \PHPUnit\Framework\TestCase
{
    protected RequestBuilder $mockRequestBuilder;
    protected OrderFactory $mockOrderFactory;
    protected Order $mockOrder;
    protected StoreManagerInterface $mockStoreManager;
    protected Store $mockStore;
    protected OrderRequestService $orderRequestService;

    protected function setUp(): void
    {
        $this->mockRequestBuilder = $this->createMock(RequestBuilder::class);
        $this->mockOrderFactory = $this->createMock(OrderFactory::class);
        $this->mockOrder = $this->createMock(Order::class);
        $this->mockStoreManager = $this->createMock(StoreManagerInterface::class);
        $this->mockStore = $this->createMock(Store::class);
        $this->orderRequestService = new OrderRequestService(
            $this->mockRequestBuilder,
            $this->mockOrderFactory,
            $this->mockStoreManager
        );
    }

    public function testExport()
    {
        $reference = '10000001';

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('setRequestData')
            ->with(
                'Export',
                'Order',
                'Export'
            );

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('addRequestLine')
            ->with(
                json_encode([
                    'increment_id' => $reference
                ])
            );

        $this->mockOrderFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->mockOrder);

        $this->mockOrder
            ->expects($this->once())
            ->method('loadByIncrementId')
            ->with($reference)
            ->willReturnSelf();

        $this->mockOrder
            ->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $this->mockOrder
            ->expects($this->once())
            ->method('getStoreId')
            ->willReturn(1);

        $this->mockStoreManager
            ->expects($this->once())
            ->method('getStore')
            ->with(1)
            ->willReturn($this->mockStore);

        $this->mockStore
            ->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn(2);

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('setScopeId')
            ->with(2);

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('saveRequest')
            ->willReturn($this->createMock(\RealtimeDespatch\OrderFlow\Model\Request::class));


        $expectedResponse = (new \DateTime)->format('Y-m-d\TH:i:s');
        $result = $this->orderRequestService->export($reference);
        $this->assertEquals($expectedResponse, $result);

    }
}
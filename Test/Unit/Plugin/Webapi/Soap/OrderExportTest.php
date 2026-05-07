<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Plugin\Webapi\Soap;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\OrderRepository;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface;
use RealtimeDespatch\OrderFlow\Plugin\Webapi\Soap\OrderExport;

class OrderExportTest extends \PHPUnit\Framework\TestCase
{
    protected OrderExport $plugin;
    protected \Magento\Framework\ObjectManagerInterface $mockObjectManager;
    protected RequestBuilderInterface $mockRequestBuilder;
    protected OrderRepositoryInterface $mockOrderRepository;
    protected StoreManagerInterface $mockStoreManager;
    protected Store $mockStore;

    protected function setUp(): void
    {
        $this->mockObjectManager = $this->createMock(\Magento\Framework\ObjectManagerInterface::class);
        $this->mockRequestBuilder = $this->getMockBuilder(RequestBuilderInterface::class)
            ->disableOriginalConstructor()
            ->addMethods(['setRequestData', 'setRequestBody', 'setResponseBody', 'setScopeId', 'addRequestLine'])
            ->onlyMethods(['saveRequest'])
            ->getMock();
        $this->mockOrderRepository = $this->createMock(OrderRepository::class);
        $this->mockStoreManager = $this->createMock(StoreManagerInterface::class);
        $this->mockStore = $this->createMock(Store::class);

        $this->plugin = new OrderExport(
            $this->mockObjectManager,
            $this->mockRequestBuilder,
            $this->mockOrderRepository,
            $this->mockStoreManager
        );
    }

    public function testAroundCall(): void
    {
        $mockRequestProcessor = $this->createMock(\RealtimeDespatch\OrderFlow\Model\Service\Request\RequestProcessor::class);
        $mockOrder = $this->createMock(\Magento\Sales\Model\Order::class);

        $this->mockObjectManager
            ->expects($this->once())
            ->method('create')
            ->with('OrderExportRequestProcessor')
            ->willReturn($mockRequestProcessor);

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('setRequestData')
            ->with(
                'Export',
                'Order',
                'Export',
            )
            ->willReturnSelf();

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('setRequestBody')
            ->willReturnSelf();

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('setScopeId')
            ->with(2)
            ->willReturnSelf();

        $mockRequest = $this->createMock(\RealtimeDespatch\OrderFlow\Model\Request::class);

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('saveRequest')
            ->willReturn($mockRequest);

        $this->mockOrderRepository
            ->expects($this->once())
            ->method('get')
            ->with(1)
            ->willReturn($mockOrder);

        $mockOrder
            ->expects($this->once())
            ->method('getStoreId')
            ->willReturn(1);

        $mockOrder
            ->expects($this->once())
            ->method('getIncrementId')
            ->willReturn('100000001');

        $this->mockStoreManager
            ->expects($this->once())
            ->method('getStore')
            ->with(1)
            ->willReturn($this->mockStore);

        $this->mockStore
            ->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn(2);

        $result = $this->plugin->around__call(
            $this->createMock(\Magento\Webapi\Controller\Soap\Request\Handler::class),
            function() {
                return ['result' => 'success'];
            },
            'salesOrderRepositoryV1Get',
            [
                (object) ['id' => 1]
            ]
        );

        $this->assertEquals(['result' => 'success'], $result);
    }

}
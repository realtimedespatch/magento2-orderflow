<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Plugin\Webapi\Soap;

use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\OrderRepositoryFactory;
use RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface;
use RealtimeDespatch\OrderFlow\Api\RequestRepositoryInterface;
use RealtimeDespatch\OrderFlow\Model\Runtime\OrderRepositoryRefreshContext;
use RealtimeDespatch\OrderFlow\Plugin\Webapi\Soap\OrderExport;

class OrderExportTest extends \PHPUnit\Framework\TestCase
{
    protected OrderExport $plugin;
    protected \Magento\Framework\ObjectManagerInterface $mockObjectManager;
    protected RequestBuilderInterface $mockRequestBuilder;
    protected RequestRepositoryInterface $mockRequestRepository;
    protected OrderRepositoryFactory $mockOrderRepositoryFactory;
    protected OrderRepositoryRefreshContext $mockOrderRepositoryRefreshContext;

    protected function setUp(): void
    {
        $this->mockObjectManager = $this->createMock(\Magento\Framework\ObjectManagerInterface::class);
        $this->mockRequestBuilder = $this->getMockBuilder(RequestBuilderInterface::class)
            ->disableOriginalConstructor()
            ->addMethods(['setRequestData', 'setRequestBody', 'addRequestLine'])
            ->onlyMethods(['saveRequest'])
            ->getMock();
        $this->mockRequestRepository = $this->createMock(RequestRepositoryInterface::class);
        $this->mockOrderRepositoryFactory = $this->createMock(OrderRepositoryFactory::class);
        $this->mockOrderRepositoryRefreshContext = $this->createMock(OrderRepositoryRefreshContext::class);

        $this->plugin = new OrderExport(
            $this->mockObjectManager,
            $this->mockRequestBuilder,
            $this->mockRequestRepository,
            $this->mockOrderRepositoryFactory,
            $this->mockOrderRepositoryRefreshContext
        );
    }

    public function testAroundCallExportsBeforeProceedAndForcesFreshRepositoryDuringProceed(): void
    {
        $mockRequestProcessor = $this->createMock(\RealtimeDespatch\OrderFlow\Model\Service\Request\RequestProcessor::class);
        $freshOrderRepository = $this->createMock(OrderRepository::class);
        $mockOrder = $this->createMock(\Magento\Sales\Model\Order::class);
        $mockRequest = $this->createMock(\RealtimeDespatch\OrderFlow\Model\Request::class);
        $response = [
            'result' => (object) [
                'entityId' => 1,
                'baseGrandTotal' => 100.00,
            ],
        ];
        $processed = false;

        $this->mockObjectManager
            ->expects($this->once())
            ->method('create')
            ->with('OrderExportRequestProcessor')
            ->willReturn($mockRequestProcessor);

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('setRequestData')
            ->with('Export', 'Order', 'Export')
            ->willReturnSelf();

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('setRequestBody')
            ->willReturnSelf();

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('addRequestLine')
            ->with(json_encode(['increment_id' => '100000001']))
            ->willReturnSelf();

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('saveRequest')
            ->willReturn($mockRequest);

        $this->mockOrderRepositoryFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($freshOrderRepository);

        $freshOrderRepository
            ->expects($this->once())
            ->method('get')
            ->with(1)
            ->willReturn($mockOrder);

        $mockOrder
            ->expects($this->once())
            ->method('getIncrementId')
            ->willReturn('100000001');

        $mockRequestProcessor
            ->expects($this->once())
            ->method('process')
            ->with($mockRequest)
            ->willReturnCallback(function () use (&$processed) {
                $processed = true;
                return null;
            });

        $this->mockOrderRepositoryRefreshContext
            ->expects($this->once())
            ->method('runForOrderId')
            ->with(1, $this->isType('callable'))
            ->willReturnCallback(function (int $orderId, callable $callback) {
                return $callback();
            });

        $mockRequest
            ->expects($this->once())
            ->method('setResponseBody')
            ->with(json_encode($response['result']))
            ->willReturnSelf();

        $this->mockRequestRepository
            ->expects($this->once())
            ->method('save')
            ->with($mockRequest)
            ->willReturn($mockRequest);

        $result = $this->plugin->around__call(
            $this->createMock(\Magento\Webapi\Controller\Soap\Request\Handler::class),
            function () use (&$processed, $response) {
                $this->assertTrue($processed);

                return $response;
            },
            'salesOrderRepositoryV1Get',
            [
                (object) ['id' => 1]
            ]
        );

        $this->assertSame($response, $result);
    }
}

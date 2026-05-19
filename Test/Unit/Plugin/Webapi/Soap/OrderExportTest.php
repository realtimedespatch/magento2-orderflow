<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Plugin\Webapi\Soap;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\OrderRepository;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface;
use RealtimeDespatch\OrderFlow\Api\RequestRepositoryInterface;
use RealtimeDespatch\OrderFlow\Plugin\Webapi\Soap\OrderExport;

class OrderExportTest extends \PHPUnit\Framework\TestCase
{
    protected OrderExport $plugin;
    protected \Magento\Framework\ObjectManagerInterface $mockObjectManager;
    protected RequestBuilderInterface $mockRequestBuilder;
    protected OrderRepositoryInterface $mockOrderRepository;
    protected OrderFactory $mockOrderFactory;
    protected StoreManagerInterface $mockStoreManager;
    protected RequestRepositoryInterface $mockRequestRepository;
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
        $this->mockOrderFactory = $this->createMock(OrderFactory::class);
        $this->mockStoreManager = $this->createMock(StoreManagerInterface::class);
        $this->mockRequestRepository = $this->createMock(RequestRepositoryInterface::class);
        $this->mockStore = $this->createMock(Store::class);

        $this->plugin = new OrderExport(
            $this->mockObjectManager,
            $this->mockRequestBuilder,
            $this->mockOrderRepository,
            $this->mockOrderFactory,
            $this->mockStoreManager,
            $this->mockRequestRepository
        );
    }

    public function testAroundCallExportsAfterSuccessfulProceedAndUpdatesResponse(): void
    {
        $mockRequestProcessor = $this->createMock(\RealtimeDespatch\OrderFlow\Model\Service\Request\RequestProcessor::class);
        $mockOrder = $this->createMock(\Magento\Sales\Model\Order::class);
        $freshOrder = $this->createMock(\Magento\Sales\Model\Order::class);
        $mockRequest = $this->createMock(\RealtimeDespatch\OrderFlow\Model\Request::class);
        $response = [
            'result' => (object) [
                'entityId' => 1,
                'baseGrandTotal' => 100.00,
            ],
        ];
        $proceeded = false;

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
            ->method('setScopeId')
            ->with(2)
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

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('setResponseBody')
            ->with(json_encode($response['result']))
            ->willReturnSelf();

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

        $mockRequestProcessor
            ->expects($this->once())
            ->method('process')
            ->with($mockRequest)
            ->willReturnCallback(function () use (&$proceeded) {
                $this->assertTrue($proceeded);
            });

        $this->mockOrderFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($freshOrder);

        $freshOrder
            ->expects($this->once())
            ->method('load')
            ->with(1)
            ->willReturnSelf();

        $freshOrder
            ->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $freshOrder
            ->expects($this->exactly(2))
            ->method('getData')
            ->willReturnMap([
                ['orderflow_export_status', null, 'Exported'],
                ['orderflow_export_date', null, '2026-05-19 10:15:00'],
            ]);

        $mockRequest
            ->expects($this->once())
            ->method('setResponseBody')
            ->with($this->callback(function ($body) {
                $decoded = json_decode($body, true);

                return ($decoded['extensionAttributes']['orderflowExportStatus'] ?? null) === 'Exported'
                    && ($decoded['extensionAttributes']['orderflowExportDate'] ?? null) === '2026-05-19 10:15:00';
            }))
            ->willReturnSelf();

        $this->mockRequestRepository
            ->expects($this->once())
            ->method('save')
            ->with($mockRequest)
            ->willReturn($mockRequest);

        $result = $this->plugin->around__call(
            $this->createMock(\Magento\Webapi\Controller\Soap\Request\Handler::class),
            function () use (&$proceeded, $response) {
                $proceeded = true;
                return $response;
            },
            'salesOrderRepositoryV1Get',
            [
                (object) ['id' => 1]
            ]
        );

        $this->assertSame('Exported', $result['result']->extensionAttributes->orderflowExportStatus);
        $this->assertSame('2026-05-19 10:15:00', $result['result']->extensionAttributes->orderflowExportDate);
    }

    public function testAroundCallDoesNotExportWhenProceedFails(): void
    {
        $this->mockObjectManager
            ->expects($this->never())
            ->method('create');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('SOAP failure');

        $this->plugin->around__call(
            $this->createMock(\Magento\Webapi\Controller\Soap\Request\Handler::class),
            function () {
                throw new \RuntimeException('SOAP failure');
            },
            'salesOrderRepositoryV1Get',
            [
                (object) ['id' => 1]
            ]
        );
    }

    public function testAroundCallLeavesNonOrderExportOperationsUnchanged(): void
    {
        $this->mockObjectManager
            ->expects($this->never())
            ->method('create');

        $result = $this->plugin->around__call(
            $this->createMock(\Magento\Webapi\Controller\Soap\Request\Handler::class),
            function () {
                return ['result' => 'success'];
            },
            'catalogProductRepositoryV1Get',
            [
                (object) ['sku' => 'ABC']
            ]
        );

        $this->assertEquals(['result' => 'success'], $result);
    }
}

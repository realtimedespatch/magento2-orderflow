<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Plugin\Soap;

use Magento\Sales\Model\Order;
use PHPUnit\Framework\TestCase;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Webapi\Controller\Soap\Request\Handler;
use RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Plugin\Webapi\Soap\OrderExportRequest;

class OrderExportTest extends TestCase
{
    protected $driver;
    protected $orderRepository;
    protected $handler;
    protected $requestBuilder;
    protected $plugin;

    public function setUp()
    {
        $this->driver = $this->getMockBuilder(DriverInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->orderRepository = $this->getMockBuilder(OrderRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->handler = $this->getMockBuilder(Handler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestBuilder = $this->getMockBuilder(RequestBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->plugin = new OrderExportRequest(
            $this->driver,
            $this->orderRepository,
            $this->requestBuilder
        );
    }

    public function testAroundCallForInvalidRequestOperation()
    {
        $operation  = 'realtimeDespatchOrderFlowMakeBelieveRequestManagementV1Update';
        $expectedResult = ['result' => ['dummy response']];
        $args = ['id' => 1000];
        $callable = function () {
            return ['result' => ['dummy response']];
        };

        $this->orderRepository->expects($this->never())
            ->method('get');

        $this->requestBuilder->expects($this->never())
            ->method('saveRequest');

        $actualResult = $this->plugin->around__call(
            $this->handler,
            $callable,
            $operation,
            $args
        );

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testAroundCallForNullResult()
    {
        $operation  = 'salesOrderRepositoryV1Get';
        $expectedResult = ['result' => null];
        $args = ['id' => 1000];
        $callable = function () {
            return ['result' => null];
        };

        $this->orderRepository->expects($this->never())
            ->method('get');

        $this->requestBuilder->expects($this->never())
            ->method('saveRequest');

        $actualResult = $this->plugin->around__call(
            $this->handler,
            $callable,
            $operation,
            $args
        );

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testAroundCallForNullOrderId()
    {
        $operation  = 'salesOrderRepositoryV1Get';
        $expectedResult = ['result' => ['dummy response']];
        $args = ['id' => null];
        $callable = function () {
            return ['result' => ['dummy response']];
        };

        $this->orderRepository->expects($this->never())
            ->method('get');

        $this->requestBuilder->expects($this->never())
            ->method('saveRequest');

        $actualResult = $this->plugin->around__call(
            $this->handler,
            $callable,
            $operation,
            $args
        );

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testAroundCallForInvalidOrderId()
    {
        $orderId = 1;
        $operation  = 'salesOrderRepositoryV1Get';
        $expectedResult = ['result' => ['dummy response']];

        $args = [];
        $argsWrapper = new \stdClass();
        $argsWrapper->id = $orderId;
        $args[] = $argsWrapper;

        $callable = function () {
            return ['result' => ['dummy response']];
        };

        $this->orderRepository->expects($this->once())
            ->method('get')
            ->with($orderId)
            ->willThrowException(new \Exception('Invalid Order Id'));

        $this->requestBuilder->expects($this->never())
            ->method('saveRequest');

        $actualResult = $this->plugin->around__call(
            $this->handler,
            $callable,
            $operation,
            $args
        );

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testAroundCallForValidRequest()
    {
        $orderId = 1;
        $incrementId = 666666;
        $operation  = 'salesOrderRepositoryV1Get';
        $expectedResult = ['result' => ['dummy response']];
        $requestBody = '<?xml version="1.0" encoding="UTF-8"?>
                        <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
                          <SOAP-ENV:Body>
                          </SOAP-ENV:Body>
                        </SOAP-ENV:Envelope>';

        $args = [];
        $argsWrapper = new \stdClass();
        $argsWrapper->id = $orderId;
        $args[] = $argsWrapper;

        $callable = function () {
            return ['result' => ['dummy response']];
        };

        $this->driver->expects($this->once())
            ->method('fileGetContents')
            ->with($this->equalTo('php://input'))
            ->willReturn($requestBody);

        $order = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();

        $order->expects($this->once())
            ->method('getIncrementId')
            ->willReturn($incrementId);

        $this->orderRepository->expects($this->once())
            ->method('get')
            ->with($orderId)
            ->willReturn($order);

        $this->requestBuilder->expects($this->once())
            ->method('setRequestBody')
            ->with($requestBody);

        $this->requestBuilder->expects($this->once())
            ->method('setResponseBody')
            ->with(json_encode($expectedResult['result']));

        $this->requestBuilder->expects($this->once())
            ->method('addRequestLine')
            ->with(json_encode(['increment_id' => $incrementId]));

        $this->requestBuilder->expects($this->once())
            ->method('saveRequest')
            ->with(
                RequestInterface::TYPE_EXPORT,
                RequestInterface::ENTITY_ORDER,
                RequestInterface::OP_EXPORT
            );

        $actualResult = $this->plugin->around__call(
            $this->handler,
            $callable,
            $operation,
            $args
        );

        $this->assertEquals($expectedResult, $actualResult);
    }
}

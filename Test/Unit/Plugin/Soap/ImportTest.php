<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Plugin\Soap;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\Session\Generic;
use Magento\Webapi\Controller\Soap\Request\Handler;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Api\RequestRepositoryInterface;
use RealtimeDespatch\OrderFlow\Plugin\Webapi\Soap\ImportRequest;

class ImportTest extends TestCase
{
    /**
     * @var Generic|MockObject
     */
    protected $session;

    /**
     * @var Handler|MockObject
     */
    protected $handler;

    /**
     * @var MockObject|RequestRepositoryInterface
     */
    protected $requestRepository;

    /**
     * @var ImportRequest
     */
    protected $plugin;

    public function setUp()
    {
        $this->session = $this->getMockBuilder(Generic::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->handler = $this->getMockBuilder(Handler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestRepository = $this->getMockBuilder(RequestRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->plugin = new ImportRequest(
            $this->session,
            $this->requestRepository
        );
    }

    public function testAroundCallWithNullRequestId()
    {
        $operation  = ImportRequest::OP_SHIPMENT_IMPORT;
        $expectedResult = ['result' => 'dummy response'];
        $args = [];
        $callable = function () {
            return ['result' => 'dummy response'];
        };

        $this->session->expects($this->once())
            ->method('getData')
            ->with($this->equalTo('request_id'))
            ->willReturn(null);

        $this->requestRepository->expects($this->never())
            ->method('get');

        $this->requestRepository->expects($this->never())
            ->method('save');

        $actualResult = $this->plugin->around__call(
            $this->handler,
            $callable,
            $operation,
            $args
        );

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testAroundCallForInvalidRequestId()
    {
        $requestId = 666;
        $operation  = ImportRequest::OP_SHIPMENT_IMPORT;
        $expectedResult = ['result' => 'dummy response'];
        $args = [];
        $callable = function () {
            return ['result' => 'dummy response'];
        };

        $jsonResponse = json_encode($expectedResult['result']);

        $this->session->expects($this->once())
            ->method('getData')
            ->with($this->equalTo('request_id'))
            ->willReturn($requestId);

        $this->requestRepository->expects($this->once())
            ->method('get')
            ->with($this->equalTo($requestId))
            ->willThrowException(new \Exception('Exception'));

        $this->requestRepository->expects($this->never())
            ->method('save');

        $actualResult = $this->plugin->around__call(
            $this->handler,
            $callable,
            $operation,
            $args
        );

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testAroundCallForInvalidRequestOperation()
    {
        $requestId = 666;
        $operation  = 'realtimeDespatchOrderFlowMakeBelieveRequestManagementV1Update';
        $expectedResult = ['result' => 'dummy response'];
        $args = [];
        $callable = function () {
            return ['result' => 'dummy response'];
        };

        $this->session->expects($this->once())
            ->method('getData')
            ->with($this->equalTo('request_id'))
            ->willReturn($requestId);

        $this->requestRepository->expects($this->never())
            ->method('get');

        $this->requestRepository->expects($this->never())
            ->method('save');

        $actualResult = $this->plugin->around__call(
            $this->handler,
            $callable,
            $operation,
            $args
        );

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @dataProvider dataProviderAroundCallForValidRequestOperation
     * @param $operation
     */
    public function testAroundCallForValidRequestOperation($operation)
    {
        $requestId = 666;
        $expectedResult = ['result' => 'dummy response'];
        $args = [];
        $callable = function () {
            return ['result' => 'dummy response'];
        };

        $jsonResponse = json_encode($expectedResult['result']);

        $this->session->expects($this->once())
            ->method('getData')
            ->with($this->equalTo('request_id'))
            ->willReturn($requestId);

        $request = $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->once())
            ->method('setResponseBody')
            ->with($this->equalTo($jsonResponse));

        $this->requestRepository->expects($this->once())
            ->method('get')
            ->with($this->equalTo($requestId))
            ->willReturn($request);

        $this->requestRepository->expects($this->once())
            ->method('save')
            ->with($request);

        $actualResult = $this->plugin->around__call(
            $this->handler,
            $callable,
            $operation,
            $args
        );

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @return array
     */
    public function dataProviderAroundCallForValidRequestOperation()
    {
        return [[ImportRequest::OP_INVENTORY_IMPORT], [ImportRequest::OP_SHIPMENT_IMPORT]];
    }
}

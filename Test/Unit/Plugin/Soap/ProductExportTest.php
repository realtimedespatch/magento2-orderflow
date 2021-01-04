<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Plugin\Soap;

use PHPUnit\Framework\TestCase;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Webapi\Controller\Soap\Request\Handler;
use RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Plugin\Webapi\Soap\ProductExportRequest;

class ProductExportTest extends TestCase
{
    protected $driver;
    protected $handler;
    protected $requestBuilder;
    protected $plugin;

    public function setUp()
    {
        $this->driver = $this->getMockBuilder(DriverInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->handler = $this->getMockBuilder(Handler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestBuilder = $this->getMockBuilder(RequestBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->plugin = new ProductExportRequest(
            $this->driver,
            $this->requestBuilder
        );
    }

    public function testAroundCallForInvalidRequestOperation()
    {
        $sku = 'TEST-PRODUCT';
        $operation  = 'realtimeDespatchProductFlowMakeBelieveRequestManagementV1Update';
        $expectedResult = ['result' => ['dummy response']];
        $callable = function () {
            return ['result' => ['dummy response']];
        };

        $args = [];
        $argsWrapper = new \stdClass();
        $argsWrapper->sku = $sku;
        $args[] = $argsWrapper;

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
        $sku = 'TEST-PRODUCT';
        $operation  = 'catalogProductRepositoryV1Get';
        $expectedResult = ['result' => null];
        $callable = function () {
            return ['result' => null];
        };

        $args = [];
        $argsWrapper = new \stdClass();
        $argsWrapper->sku = $sku;
        $args[] = $argsWrapper;

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

    public function testAroundCallForNullSku()
    {
        $operation  = 'catalogProductRepositoryV1Get';
        $expectedResult = ['result' => ['dummy response']];
        $callable = function () {
            return ['result' => ['dummy response']];
        };

        $args = [];
        $argsWrapper = new \stdClass();
        $argsWrapper->sku = null;
        $args[] = $argsWrapper;

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
        $sku = 'TEST-PRODUCT';
        $operation  = 'catalogProductRepositoryV1Get';
        $expectedResult = ['result' => ['dummy response']];
        $requestBody = '<?xml version="1.0" encoding="UTF-8"?>
                        <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
                          <SOAP-ENV:Body>
                          </SOAP-ENV:Body>
                        </SOAP-ENV:Envelope>';

        $args = [];
        $argsWrapper = new \stdClass();
        $argsWrapper->sku = $sku;
        $args[] = $argsWrapper;

        $callable = function () {
            return ['result' => ['dummy response']];
        };

        $this->driver->expects($this->once())
            ->method('fileGetContents')
            ->with($this->equalTo('php://input'))
            ->willReturn($requestBody);

        $this->requestBuilder->expects($this->once())
            ->method('setRequestBody')
            ->with($requestBody);

        $this->requestBuilder->expects($this->once())
            ->method('setResponseBody')
            ->with(json_encode($expectedResult['result']));

        $this->requestBuilder->expects($this->once())
            ->method('addRequestLine')
            ->with(json_encode(['sku' => $sku]));

        $this->requestBuilder->expects($this->once())
            ->method('saveRequest')
            ->with(
                RequestInterface::TYPE_EXPORT,
                RequestInterface::ENTITY_PRODUCT,
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

    public function testAroundCallForExceptionOnRequestSave()
    {
        $sku = 'TEST-PRODUCT';
        $operation  = 'catalogProductRepositoryV1Get';
        $expectedResult = ['result' => ['dummy response']];
        $requestBody = '<?xml version="1.0" encoding="UTF-8"?>
                        <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
                          <SOAP-ENV:Body>
                          </SOAP-ENV:Body>
                        </SOAP-ENV:Envelope>';

        $args = [];
        $argsWrapper = new \stdClass();
        $argsWrapper->sku = $sku;
        $args[] = $argsWrapper;

        $callable = function () {
            return ['result' => ['dummy response']];
        };

        $this->driver->expects($this->once())
            ->method('fileGetContents')
            ->with($this->equalTo('php://input'))
            ->willReturn($requestBody);

        $this->requestBuilder->expects($this->once())
            ->method('setRequestBody')
            ->with($requestBody);

        $this->requestBuilder->expects($this->once())
            ->method('setResponseBody')
            ->with(json_encode($expectedResult['result']));

        $this->requestBuilder->expects($this->once())
            ->method('addRequestLine')
            ->with(json_encode(['sku' => $sku]));

        $this->requestBuilder->expects($this->once())
            ->method('saveRequest')
            ->with(
                RequestInterface::TYPE_EXPORT,
                RequestInterface::ENTITY_PRODUCT,
                RequestInterface::OP_EXPORT
            )
            ->willThrowException(new \Exception('Cannot save request'));

        $actualResult = $this->plugin->around__call(
            $this->handler,
            $callable,
            $operation,
            $args
        );

        $this->assertEquals($expectedResult, $actualResult);
    }
}

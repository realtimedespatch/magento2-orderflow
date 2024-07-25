<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Builder;

use RealtimeDespatch\OrderFlow\Api\RequestRepositoryInterface;
use RealtimeDespatch\OrderFlow\Model\Builder\RequestBuilder;
use RealtimeDespatch\OrderFlow\Model\Request;
use RealtimeDespatch\OrderFlow\Model\RequestFactory;
use RealtimeDespatch\OrderFlow\Model\RequestLine;
use RealtimeDespatch\OrderFlow\Model\RequestLineFactory;

class RequestBuilderTest extends \PHPUnit\Framework\TestCase
{
    protected RequestBuilder $requestBuilder;
    protected RequestFactory $mockRequestFactory;
    protected RequestLineFactory $mockRequestLineFactory;
    protected RequestRepositoryInterface $mockRequestRepository;
    protected Request $mockRequest;
    protected Request $mockRequest2;

    protected function setUp(): void
    {
        $this->mockRequestFactory = $this->createMock(RequestFactory::class);
        $this->mockRequestLineFactory = $this->createMock(RequestLineFactory::class);
        $this->mockRequestRepository = $this->createMock(RequestRepositoryInterface::class);
        $this->mockRequest = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'setType', 'setEntity', 'setOperation',
                'addLine', 'setCreatedAt', 'setProcessedAt',
                'setRequestBody', 'setResponseBody', 'setScopeId',
            ])
            ->addMethods(['setSequenceId'])
            ->getMock();

        $this->mockRequest2 = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getId'])
            ->getMock();

        $this->mockRequestFactory
            ->method('create')
            ->willReturnOnConsecutiveCalls([
                $this->mockRequest,
                $this->mockRequest2,
            ]);

        $this->requestBuilder = new RequestBuilder(
            $this->mockRequestFactory,
            $this->mockRequestLineFactory,
            $this->mockRequestRepository
        );
    }

    public function testSetRequestData(): void
    {
        $this->mockRequest
            ->expects($this->once())
            ->method('setType')
            ->with('Import');

        $this->mockRequest
            ->expects($this->once())
            ->method('setEntity')
            ->with('Inventory');

        $this->mockRequest
            ->expects($this->once())
            ->method('setOperation')
            ->with('Update');

        $this->mockRequest
            ->expects($this->once())
            ->method('setSequenceId')
            ->with(123);

        $result = $this->requestBuilder->setRequestData('Import', 'Inventory', 'Update', 123);
        $this->assertEquals($this->requestBuilder, $result);
    }

    public function testAddRequestLine(): void
    {
        $mockRequestLine = $this->createMock(RequestLine::class);

        $this->mockRequestLineFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($mockRequestLine);

        $mockRequestLine
            ->expects($this->once())
            ->method('setBody')
            ->with('Example Body');

        $mockRequestLine
            ->expects($this->once())
            ->method('setSequenceId')
            ->with(123);

        $this->mockRequest
            ->expects($this->once())
            ->method('addLine')
            ->with($mockRequestLine);

        $result = $this->requestBuilder->addRequestLine('Example Body', 123);
        $this->assertEquals($this->requestBuilder, $result);
    }

    public function testMarkProcessed(): void
    {
        $processedDate = date('Y-m-d H:i:s');

        $this->mockRequest
            ->expects($this->once())
            ->method('setCreatedAt')
            ->with($processedDate);

        $this->mockRequest
            ->expects($this->once())
            ->method('setProcessedAt')
            ->with($processedDate);

        $result = $this->requestBuilder->markProcessed($processedDate);
        $this->assertNull($result);
    }

    public function testSetRequestBody(): void
    {
        $this->mockRequest
            ->expects($this->once())
            ->method('setRequestBody')
            ->with('Example Body');

        $result = $this->requestBuilder->setRequestBody('Example Body');
        $this->assertEquals($this->requestBuilder, $result);
    }

    public function testSetResponseBody(): void
    {
        $this->mockRequest
            ->expects($this->once())
            ->method('setResponseBody')
            ->with('Example Body');

        $result = $this->requestBuilder->setResponseBody('Example Body');
        $this->assertEquals($this->requestBuilder, $result);
    }

    public function testSetScopeBody(): void
    {
        $this->mockRequest
            ->expects($this->once())
            ->method('setScopeId')
            ->with(123);

        $result = $this->requestBuilder->setScopeId(123);
        $this->assertEquals($this->requestBuilder, $result);
    }

    public function testResetBuilder(): void
    {
        $mockRequest2 = $this->createMock(Request::class);
        $mockRequest2
            ->method('getId'
            )->willReturn(124);

        $this->mockRequestFactory
            ->expects($this->at(2))
            ->method('create')
            ->willReturn($mockRequest2);

        $result = $this->requestBuilder->resetBuilder();
        $this->assertEquals($this->requestBuilder, $result);
        $this->assertNotEquals(
            $this->mockRequest->getId(),
            $this->requestBuilder->getRequest()->getId()
        );
    }

    public function testSaveRequest(): void
    {
        $this->mockRequestRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->mockRequest)
            ->willReturn($this->mockRequest);

        $result = $this->requestBuilder->saveRequest();
        $this->assertEquals($this->mockRequest, $result);
    }
}
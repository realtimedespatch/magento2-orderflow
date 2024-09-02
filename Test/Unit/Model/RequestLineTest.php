<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model;

use RealtimeDespatch\OrderFlow\Model\RequestLine;

class RequestLineTest extends AbstractModelTest
{
    protected RequestLine $requestLine;

    protected function setUp(): void
    {
        parent::setUp();

        $this->idFieldName = 'line_id';

        $this->requestLine = new RequestLine(
            $this->mockContext,
            $this->mockRegistry,
            $this->mockResource,
            $this->mockResourceCollection
        );
    }

    public function testData(): void
    {
        $this->requestLine->setData(['line_id' => 1]);
        $this->assertEquals(1, $this->requestLine->getId());

        $mockRequest = $this->createMock(\RealtimeDespatch\OrderFlow\Model\Request::class);
        $mockRequest
            ->expects($this->once())
            ->method('getEntity')
            ->willReturn('Order');

        $this->assertNull($this->requestLine->getRequest());
        $this->assertNull($this->requestLine->getType());
        $this->requestLine->setRequest($mockRequest);

        $this->assertEquals('Order', $this->requestLine->getType());
        $this->assertEquals($mockRequest, $this->requestLine->getRequest());

        $this->requestLine->setRequestId(2);
        $this->assertEquals(2, $this->requestLine->getRequestId());

        $this->requestLine->setResponse('Test Response');
        $this->assertEquals('Test Response', $this->requestLine->getResponse());

        $this->requestLine->setBody('["Test Body"]');
        $this->assertEquals(["Test Body"], $this->requestLine->getBody());

        $this->requestLine->setSequenceId(3);
        $this->assertEquals(3, $this->requestLine->getSequenceId());

        $this->requestLine->setCreatedAt('2021-01-01 00:00:00');
        $this->assertEquals('2021-01-01 00:00:00', $this->requestLine->getCreatedAt());

        $this->requestLine->setProcessedAt('2022-01-01 00:00:00');
        $this->assertEquals('2022-01-01 00:00:00', $this->requestLine->getProcessedAt());

        $this->requestLine->setMessageId(4);
        $this->assertEquals(4, $this->requestLine->getMessageId());
    }
}
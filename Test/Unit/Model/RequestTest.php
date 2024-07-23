<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model;

use RealtimeDespatch\OrderFlow\Model\Request;
use RealtimeDespatch\OrderFlow\Model\RequestLine;
use RealtimeDespatch\OrderFlow\Model\RequestLineFactory;

class RequestTest extends AbstractModelTest
{
    protected Request $request;
    protected RequestLineFactory $mockRequestLineFactory;
    protected RequestLine $mockRequestLine;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockRequestLineFactory = $this->createMock(RequestLineFactory::class);
        $this->mockRequestLine = $this->createMock(RequestLine::class);

        $this->mockRequestLineFactory
            ->method('create')
            ->willReturn($this->mockRequestLine);

        $this->mockRequestLine
            ->method('getCollection')
            ->willReturn($this->mockResourceCollection);

        $this->request = new Request(
            $this->mockContext,
            $this->mockRegistry,
            $this->mockRequestLineFactory,
            $this->mockResource,
            $this->mockResourceCollection,
        );
    }

    public function testData(): void
    {
        // handles the getLines method
        $this->mockRequestLine
            ->method('getCollection')
            ->willReturn($this->mockResourceCollection);

        $this->mockResourceCollection
            ->expects($this->once())
            ->method('addFieldToFilter')
            ->with('request_id', ['eq' => 1])
            ->willReturnSelf();

        $this->mockResourceCollection
            ->expects($this->once())
            ->method('addFieldToSelect')
            ->with('*')
            ->willReturnSelf();

        $this->mockResourceCollection
            ->expects($this->once())
            ->method('load')
            ->willReturn([
                $this->createMock(RequestLine::class),
            ]);

        $this->request->setData(['request_id' => 1]);

        $this->request->setMessageId(2);
        $this->assertEquals(2, $this->request->getMessageId());

        $this->request->setScopeId(3);
        $this->assertEquals(3, $this->request->getScopeId());

        $this->request->setEntity('Order');
        $this->assertEquals('Order', $this->request->getEntity());

        $this->request->setOperation('Update');
        $this->assertEquals('Update', $this->request->getOperation());

        $this->request->setCreatedAt('2021-01-01 00:00:00');
        $this->assertEquals('2021-01-01 00:00:00', $this->request->getCreatedAt());

        $this->assertEquals('Pending', (string) $this->request->getRequestBody());
        $this->assertEquals('Pending', (string) $this->request->getResponseBody());
        $this->assertFalse($this->request->isProcessed());
        $this->assertTrue($this->request->canProcess());
        $this->request->setProcessedAt('2022-01-01 00:00:00');
        $this->assertEquals('2022-01-01 00:00:00', $this->request->getProcessedAt());
        $this->assertTrue($this->request->isProcessed());
        $this->assertFalse($this->request->canProcess());
        $this->assertEquals('Request Unavailable', (string) $this->request->getRequestBody());
        $this->assertEquals('Response Unavailable', (string) $this->request->getResponseBody());

        $this->request->setType('Test Type');
        $this->assertEquals('Test Type', $this->request->getType());

        // will throw error if _lines property not read + another db call made
        $this->request->getLines();

        // test non xml request
        $this->request->setRequestBody('<xml>Test Request Body');
        $this->assertEquals('Request Unavailable', (string) $this->request->getRequestBody());

        // test non xml response
        $this->request->setResponseBody('Test Response Body');
        $this->assertEquals('Test Response Body', (string) $this->request->getResponseBody());


        $xml = '<?xml version="1.0" encoding="UTF-8"?><message>Test Request Body</message>';
        $this->request->setRequestBody($xml);
        $this->assertEquals($xml, str_replace("\n", "", $this->request->getRequestBody()));

        $xml = '<?xml version="1.0" encoding="UTF-8"?><message>Test Request Body</message>';
        $this->request->setResponseBody($xml);
        $this->assertEquals($xml, str_replace("\n", "", $this->request->getResponseBody()));

        $this->request->setType('NotARealType');
        $this->request->setOperation('NotARealOperation');
        $this->assertFalse($this->request->isExport());
        $this->assertFalse($this->request->isImport());
        $this->assertFalse($this->request->isCreate());

        $this->request->setType('Export');
        $this->assertTrue($this->request->isExport());

        $this->request->setType('Import');
        $this->assertTrue($this->request->isImport());

        $this->request->addLine($this->mockRequestLine);
        $this->assertEquals([$this->mockRequestLine, $this->mockRequestLine], $this->request->getLines());

        $this->request->setLines([$this->mockRequestLine]);
        $this->assertEquals([$this->mockRequestLine], $this->request->getLines());
    }
}
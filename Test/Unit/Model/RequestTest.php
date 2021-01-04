<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model;

use DOMDocument;
use Exception;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Helper\Xml as XmlHelper;
use RealtimeDespatch\OrderFlow\Model\Request;
use RealtimeDespatch\OrderFlow\Model\RequestLine;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request as RequestResource;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request\Collection as RequestCollection;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\RequestLine\Collection;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\RequestLine\CollectionFactory as RequestLineCollectionFactoru;

class RequestTest extends TestCase
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var MockObject
     */
    protected $reqLineCollection;

    /**
     * @var MockObject
     */
    protected $xmlHelper;

    /**
     * Setup
     */
    protected function setUp()
    {
        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $registry = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resource = $this->getMockBuilder(RequestResource::class)
            ->disableOriginalConstructor()
            ->getMock();

        $requestCollection = $this->getMockBuilder(RequestCollection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->reqLineCollectionFactory = $this->getMockBuilder(RequestLineCollectionFactoru::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->xmlHelper = $this->getMockBuilder(XmlHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $data = [];

        $this->request = new Request(
            $context,
            $registry,
            $this->reqLineCollectionFactory,
            $this->xmlHelper,
            $resource,
            $requestCollection,
            $data
        );
    }

    public function testGetId()
    {
        $requestId = uniqid();
        $this->request->setData(Request::REQUEST_ID, $requestId);
        $this->assertEquals($requestId, $this->request->getId());
    }

    public function testSetAndGetMessageId()
    {
        $messageId = uniqid();
        $this->request->setMessageId($messageId);
        $this->assertEquals($messageId, $this->request->getMessageId());
    }

    public function testSetAndGetScopeId()
    {
        $scopeId = 1;
        $this->request->setScopeId($scopeId);
        $this->assertEquals($scopeId, $this->request->getScopeId());
    }

    public function testSetAndGetLines()
    {
        $lines = [
            ['id' => 1],
            ['id' => 2]
        ];

        $this->request->setLines($lines);
        $this->assertEquals($lines, $this->request->getLines());
    }

    /**
     * @depends testGetId
     */
    public function testGetLinesWithNoLinesSet()
    {
        $lines = [
            ['id' => 1],
            ['id' => 2]
        ];

        $requestId = uniqid();
        $this->request->setData(Request::REQUEST_ID, $requestId);

        $requestLineColl = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $requestLineColl
            ->expects($this->once())
            ->method('addFieldToSelect')
            ->with('*')
            ->willReturn($requestLineColl);

        $requestLineColl
            ->expects($this->once())
            ->method('addFieldToFilter')
            ->with('request_id', ['eq' => $this->request->getId()])
            ->willReturn($requestLineColl);

        $requestLineColl
            ->expects($this->once())
            ->method('loadData')
            ->willReturn($lines);

        $this->reqLineCollectionFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($requestLineColl);

        $this->assertEquals($lines, $this->request->getLines());
    }

    public function testSetAndGetType()
    {
        $type = RequestInterface::TYPE_EXPORT;
        $this->request->setType($type);
        $this->assertEquals($type, $this->request->getType());
    }

    public function testSetAndGetEntity()
    {
        $entity = RequestInterface::ENTITY_ORDER;
        $this->request->setEntity($entity);
        $this->assertEquals($entity, $this->request->getEntity());
    }

    public function testSetAndGetOperation()
    {
        $operation = RequestInterface::OP_EXPORT;
        $this->request->setOperation($operation);
        $this->assertEquals($operation, $this->request->getOperation());
    }

    public function testSetAndGetCreatedAt()
    {
        $createdAt = date('Y-m-d H:i:s');
        $this->request->setCreatedAt($createdAt);
        $this->assertEquals($createdAt, $this->request->getCreatedAt());
    }

    public function testSetAndGetProcessedAt()
    {
        $processedAt = date('Y-m-d H:i:s');
        $requestLine = $this->getMockBuilder(RequestLine::class)
            ->disableOriginalConstructor()
            ->getMock();

        $requestLine
            ->expects($this->once())
            ->method('setProcessedAt')
            ->willReturn($requestLine);

        $this->request->setLines([$requestLine]);
        $this->request->setProcessedAt($processedAt);
        $this->assertEquals($processedAt, $this->request->getProcessedAt());
    }

    public function testSetAndGetValidXmlResponseBody()
    {
        $responseBody = '<?xml version="1.0" encoding="UTF-8"?>
                        <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
                          <SOAP-ENV:Body>
                          </SOAP-ENV:Body>
                        </SOAP-ENV:Envelope>';

        $this->request->setResponseBody($responseBody);

        $dom = new DOMDocument;
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($responseBody);
        $dom->formatOutput = true;
        $xml = $dom->saveXml();

        $this->xmlHelper
            ->expects($this->once())
            ->method('getDomDocument')
            ->willReturn($dom);

        $this->assertEquals($xml, $this->request->getResponseBody());
    }

    public function testSetAndGetInvalidXmlResponseBody()
    {
        $responseBody = '<>';
        $this->request->setResponseBody($responseBody);

        $this->xmlHelper
            ->expects($this->once())
            ->method('getDomDocument')
            ->willThrowException(new Exception);

        $this->assertEquals($responseBody, $this->request->getResponseBody());
    }

    /**
     * @depends testSetAndGetLines
     */
    public function testGetNullResponseBodyForProcessedResponse()
    {
        $processedAt = date('Y-m-d H:i:s');
        $this->request->setLines([]);
        $this->request->setProcessedAt($processedAt);

        $expectedValue = 'Response Unavailable';

        $this->assertEquals($expectedValue, $this->request->getResponseBody());
    }

    public function testGetNullResponseBodyForUnprocessedResponse()
    {
        $expectedValue = 'Pending';

        $this->assertEquals($expectedValue, $this->request->getResponseBody());
    }

    public function testSetAndGetValidXmlRequestBody()
    {
        $requestBody = '<?xml version="1.0" encoding="UTF-8"?>
                        <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
                          <SOAP-ENV:Body>
                          </SOAP-ENV:Body>
                        </SOAP-ENV:Envelope>';

        $this->request->setRequestBody($requestBody);

        $dom = new DOMDocument;
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($requestBody);
        $dom->formatOutput = true;
        $xml = $dom->saveXml();

        $this->xmlHelper
            ->expects($this->once())
            ->method('getDomDocument')
            ->willReturn($dom);

        $this->assertEquals($xml, $this->request->getRequestBody());
    }

    public function testSetAndGetInvalidXmlRequestBody()
    {
        $responseBody = '<>';
        $this->request->setRequestBody($responseBody);

        $this->xmlHelper
            ->expects($this->once())
            ->method('getDomDocument')
            ->willThrowException(new Exception);

        $expectedValue = 'Request Unavailable';

        $this->assertEquals($expectedValue, $this->request->getRequestBody());
    }

    /**
     * @depends testSetAndGetLines
     */
    public function testGetNullRequestBodyForProcessedRequest()
    {
        $processedAt = date('Y-m-d H:i:s');
        $this->request->setLines([]);
        $this->request->setProcessedAt($processedAt);

        $expectedValue = 'Request Unavailable';

        $this->assertEquals($expectedValue, $this->request->getRequestBody());
    }

    public function testGetNullRequestBodyForUnprocessedRequest()
    {
        $expectedValue = 'Pending';

        $this->assertEquals($expectedValue, $this->request->getRequestBody());
    }

    public function testCanProcess()
    {
        $this->assertEquals(true, $this->request->canProcess());

        $processedAt = date('Y-m-d H:i:s');
        $this->request->setLines([]);
        $this->request->setProcessedAt($processedAt);

        $this->assertEquals(false, $this->request->canProcess());
    }

    public function testIsProcessed()
    {
        $this->assertEquals(false, $this->request->isProcessed());

        $processedAt = date('Y-m-d H:i:s');
        $this->request->setLines([]);
        $this->request->setProcessedAt($processedAt);

        $this->assertEquals(true, $this->request->isProcessed());
    }

    public function testIsExport()
    {
        $this->request->setType(Request::TYPE_IMPORT);
        $this->assertEquals(false, $this->request->isExport());

        $this->request->setType(Request::TYPE_EXPORT);
        $this->assertEquals(true, $this->request->isExport());
    }

    public function testIsImport()
    {
        $this->request->setType(Request::TYPE_EXPORT);
        $this->assertEquals(false, $this->request->isImport());

        $this->request->setType(Request::TYPE_IMPORT);
        $this->assertEquals(true, $this->request->isImport());
    }

    public function testIsCreate()
    {
        $this->request->setOperation(Request::OP_EXPORT);
        $this->assertEquals(false, $this->request->isCreate());

        $this->request->setOperation(Request::OP_CREATE);
        $this->assertEquals(true, $this->request->isCreate());
    }
}

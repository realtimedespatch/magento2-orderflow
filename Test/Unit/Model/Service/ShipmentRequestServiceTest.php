<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Service;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;
use RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface;
use RealtimeDespatch\OrderFlow\Model\Request;
use RealtimeDespatch\OrderFlow\Model\Service\ShipmentRequestService;

class ShipmentRequestServiceTest extends \PHPUnit\Framework\TestCase
{
    protected ShipmentRequestService $shipmentRequestService;
    protected Registry $mockRegistry;
    protected LoggerInterface $mockLogger;
    protected RequestBuilderInterface $mockRequestBuilder;
    protected Http $mockRequest;


    protected function setUp(): void
    {
        $this->mockRegistry = $this->createMock(Registry::class);
        $this->mockLogger = $this->createMock(LoggerInterface::class);
        $this->mockRequestBuilder = $this->getMockBuilder(RequestBuilderInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveRequest'])
            ->addMethods(['setRequestBody', 'setRequestData', 'addRequestLine', 'getRequest'])
            ->getMock();
        $this->mockRequest = $this->createMock(Http::class);

        $this->shipmentRequestService = new ShipmentRequestService(
            $this->mockRegistry,
            $this->mockLogger,
            $this->mockRequestBuilder,
            $this->mockRequest
        );
    }

    public function testCreate()
    {
        $orderIncrementId = '10000001';
        $skuQtys = [
            'SKU-001' => 10,
            'SKU-002' => 20,
        ];
        $tracks = [
            'TRACK-001' => 'CARRIER-001',
            'TRACK-002' => 'CARRIER-002',
        ];
        $comment = 'Test Comment';
        $email = 'test@example.com';
        $courierName = 'Test Courier';
        $serviceName = 'Test Service';
        $trackingNumber = 'TRACK-001';
        $dateShipped = '2021-01-01 00:00:00';
        $messageSeqId = '123456';

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('setRequestData')
            ->with(
                'Import',
                'Shipment',
                'Create',
                $messageSeqId
            );

        $this->mockRequest
            ->expects($this->once())
            ->method('getContent')
            ->willReturn('{"request": "body"}');

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('setRequestBody')
            ->with('{"request": "body"}');

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('addRequestLine')
            ->with(
                json_encode([
                    'orderIncrementId' => $orderIncrementId,
                    'skuQtys' => $skuQtys,
                    'tracks' => $tracks,
                    'comment' => $comment,
                    'email' => $email,
                    'includeComment' => true,
                    'courierName' => $courierName,
                    'serviceName' => $serviceName,
                    'trackingNumber' => $trackingNumber,
                    'dateShipped' => $dateShipped,
                    'sequenceId' => $messageSeqId,
                ]),
                $messageSeqId
            );

        $mockOrderflowRequest = $this->createMock(Request::class);
        $mockOrderflowRequest->method('getId')->willReturn(123);

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($mockOrderflowRequest);

        $this->mockRegistry
            ->expects($this->once())
            ->method('register')
            ->with('request_id', 123);

        $this->shipmentRequestService->create(
            $orderIncrementId,
            $skuQtys,
            $tracks,
            $comment,
            $email,
            true,
            $courierName,
            $serviceName,
            $trackingNumber,
            $dateShipped,
            $messageSeqId
        );
    }
}
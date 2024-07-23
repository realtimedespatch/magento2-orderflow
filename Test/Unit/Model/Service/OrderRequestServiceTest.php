<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Service;

use RealtimeDespatch\OrderFlow\Model\Builder\RequestBuilder;
use RealtimeDespatch\OrderFlow\Model\Service\OrderRequestService;

class OrderRequestServiceTest extends \PHPUnit\Framework\TestCase
{
    protected RequestBuilder $mockRequestBuilder;
    protected OrderRequestService $orderRequestService;

    protected function setUp(): void
    {
        $this->mockRequestBuilder = $this->createMock(RequestBuilder::class);
        $this->orderRequestService = new OrderRequestService($this->mockRequestBuilder);
    }

    public function testExport()
    {
        $reference = '10000001';

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('setRequestData')
            ->with(
                'Export',
                'Order',
                'Export'
            );

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('addRequestLine')
            ->with(
                json_encode([
                    'increment_id' => $reference
                ])
            );

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('saveRequest')
            ->willReturn($this->createMock(\RealtimeDespatch\OrderFlow\Model\Request::class));


        $expectedResponse = (new \DateTime)->format('Y-m-d\TH:i:s');
        $result = $this->orderRequestService->export($reference);
        $this->assertEquals($expectedResponse, $result);

    }
}
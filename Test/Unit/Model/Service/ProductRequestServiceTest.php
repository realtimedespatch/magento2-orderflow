<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Service;

use RealtimeDespatch\OrderFlow\Model\Builder\RequestBuilder;
use RealtimeDespatch\OrderFlow\Model\Service\OrderRequestService;
use RealtimeDespatch\OrderFlow\Model\Service\ProductRequestService;

class ProductRequestServiceTest extends \PHPUnit\Framework\TestCase
{
    protected RequestBuilder $mockRequestBuilder;
    protected ProductRequestService $productRequestService;

    protected function setUp(): void
    {
        $this->mockRequestBuilder = $this->createMock(RequestBuilder::class);
        $this->productRequestService = new ProductRequestService($this->mockRequestBuilder);
    }

    public function testExport()
    {
        $reference = 'SKU123';

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('setRequestData')
            ->with(
                'Export',
                'Product',
                'Export'
            );

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('addRequestLine')
            ->with(
                json_encode([
                    'sku' => $reference
                ])
            );

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('saveRequest')
            ->willReturn($this->createMock(\RealtimeDespatch\OrderFlow\Model\Request::class));


        $expectedResponse = (new \DateTime)->format('Y-m-d\TH:i:s');
        $result = $this->productRequestService->export($reference);
        $this->assertEquals($expectedResponse, $result);

    }
}
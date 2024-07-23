<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Plugin\Webapi\Soap;

use Magento\Framework\ObjectManagerInterface;
use RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface;
use RealtimeDespatch\OrderFlow\Model\Service\Request\RequestProcessor;
use RealtimeDespatch\OrderFlow\Plugin\Webapi\Soap\InventoryImport;
use RealtimeDespatch\OrderFlow\Plugin\Webapi\Soap\ProductExport;
use RealtimeDespatch\OrderFlow\Helper\Export\Product as ProductHelper;
use Magento\Webapi\Controller\Soap\Request\Handler as SoapRequestHandler;

class ProductExportTest extends \PHPUnit\Framework\TestCase
{
    protected ProductExport $plugin;
    protected ObjectManagerInterface $mockObjectManager;
    protected RequestBuilderInterface $mockRequestBuilder;
    protected ProductHelper $mockProductHelper;
    protected SoapRequestHandler $mockSoapRequestHandler;

    protected function setUp(): void
    {
        $this->mockObjectManager = $this->createMock(\Magento\Framework\ObjectManagerInterface::class);
        $this->mockProductHelper = $this->createMock(ProductHelper::class);
        $this->mockRequestBuilder = $this->getMockBuilder(RequestBuilderInterface::class)
            ->disableOriginalConstructor()
            ->addMethods(['setRequestData', 'setRequestBody', 'setResponseBody', 'addRequestLine'])
            ->onlyMethods(['saveRequest'])
            ->getMock();
        $this->mockSoapRequestHandler = $this->createMock(SoapRequestHandler::class);

        $this->plugin = new ProductExport(
            $this->mockObjectManager,
            $this->mockRequestBuilder,
            $this->mockProductHelper,
        );
    }

    /**
     * @dataProvider testAroundCallDataProvider
     * @return void
     */
    public function testAroundCall(bool $productExportEnabled): void
    {
        $mockRequestProcessor = $this->createMock(RequestProcessor::class);

        $this->mockObjectManager
            ->expects($this->once())
            ->method('create')
            ->with('ProductExportRequestProcessor')
            ->willReturn($mockRequestProcessor);

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('setRequestData')
            ->with(
                'Export',
                'Product',
                'Export',
            );

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('setRequestBody');

        $response = ['result' => 'success',];

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('setResponseBody')
            ->with(json_encode($response['result']));

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('addRequestLine')
            ->with(json_encode(['sku' => 'SKU-1234']));

        $mockRequest = $this->createMock(\RealtimeDespatch\OrderFlow\Model\Request::class);

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('saveRequest')
            ->willReturn($mockRequest);

        if (!$productExportEnabled) {
            $this->expectException(\Magento\Framework\Webapi\Exception::class);
            $this->expectExceptionMessage("Product 'SKU-1234' is not in any product export enabled websites");
        } else {
            $this->mockProductHelper
                ->expects($this->once())
                ->method('isProductExportEnabledForProductWebsites')
                ->with('SKU-1234')
                ->willReturn(true);
        }

        $this->plugin->around__call(
            $this->mockSoapRequestHandler,
            fn() => $response,
            'catalogProductRepositoryV1Get',
            [
                (object) [
                    'sku' => 'SKU-1234',
                ]
            ]
        );
    }

    public function testAroundCallIgnore(): void
    {
        $this->mockObjectManager
            ->expects($this->never())
            ->method('create')
            ->with('ProductExportRequestProcessor');

        $this->plugin->around__call(
            $this->mockSoapRequestHandler,
            function() { },
            InventoryImport::class,
            [
                (object) [
                    'sku' => 'SKU-1234',
                ]
            ]
        );
    }

    public function testAroundCallDataProvider(): array
    {
        return [
            [true],
            [false],
        ];
    }
}
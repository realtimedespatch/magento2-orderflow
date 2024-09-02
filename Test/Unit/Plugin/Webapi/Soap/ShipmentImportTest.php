<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Plugin\Webapi\Soap;

use RealtimeDespatch\OrderFlow\Plugin\Webapi\Soap\InventoryImport;
use RealtimeDespatch\OrderFlow\Plugin\Webapi\Soap\ShipmentImport;

class ShipmentImportTest extends \PHPUnit\Framework\TestCase
{
    protected ShipmentImport $plugin;
    protected \Magento\Framework\Registry $mockRegistry;
    protected \RealtimeDespatch\OrderFlow\Model\RequestFactory $mockRequestFactory;

    protected function setUp(): void
    {
        $this->mockRegistry = $this->createMock(\Magento\Framework\Registry::class);
        $this->mockRequestFactory = $this->createMock(\RealtimeDespatch\OrderFlow\Model\RequestFactory::class);
        $this->plugin = new ShipmentImport(
            $this->mockRegistry,
            $this->mockRequestFactory
        );
    }

    /**
     * @return void
     */
    public function testAroundCall(): void
    {
        $mockSoapRequestHandler = $this->createMock(\Magento\Webapi\Controller\Soap\Request\Handler::class);

        $mockProceed = function(): array {
            return ['result' => 'success'];
        };

        $mockRequest = $this->createMock(\RealtimeDespatch\OrderFlow\Model\Request::class);

        $mockRequest
            ->expects($this->once())
            ->method('load')
            ->with(1)
            ->willReturnSelf();

        $mockRequest
            ->expects($this->once())
            ->method('setResponseBody')
            ->with('"success"')
            ->willReturnSelf();

        $this->mockRequestFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($mockRequest);

        $this->mockRegistry
            ->expects($this->once())
            ->method('registry')
            ->with('request_id')
            ->willReturn(1);

        $this->assertEquals(
            ['result' => 'success'],
            $this->plugin->around__call(
                $mockSoapRequestHandler,
                $mockProceed,
                'realtimeDespatchOrderFlowShipmentRequestManagementV1Create',
                ['shipment' => 'data']
            )
        );
    }

    public function testAroundCallIgnore(): void
    {
        $this->mockRegistry
            ->expects($this->once())
            ->method('registry')
            ->with('request_id')
            ->willReturn(1);

        $this->mockRequestFactory
            ->expects($this->never())
            ->method('create');

        $this->plugin->around__call(
            $this->createMock(\Magento\Webapi\Controller\Soap\Request\Handler::class),
            function() { },
            InventoryImport::OP_SHIPMENT_IMPORT,
            ['data']
        );
    }
}
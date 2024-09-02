<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Plugin\Webapi\Soap;

use Magento\Framework\Registry;
use RealtimeDespatch\OrderFlow\Model\RequestFactory;
use RealtimeDespatch\OrderFlow\Plugin\Webapi\Soap\InventoryImport;
use RealtimeDespatch\OrderFlow\Plugin\Webapi\Soap\ShipmentImport;

class InventoryImportTest extends \PHPUnit\Framework\TestCase
{
    protected InventoryImport $plugin;
    protected RequestFactory $mockRequestFactory;
    protected Registry $mockRegistry;

    protected function setUp(): void
    {
        $this->mockRegistry = $this->createMock(\Magento\Framework\Registry::class);
        $this->mockRequestFactory = $this->createMock(RequestFactory::class);
        $this->plugin = new InventoryImport(
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

        $this->plugin->around__call(
            $mockSoapRequestHandler,
            $mockProceed,
            'realtimeDespatchOrderFlowInventoryRequestManagementV1Update',
            ['data']
        );
    }

    /**
     * @return void
     */
    public function testAroundCallIgnore(): void
    {
        $mockSoapRequestHandler = $this->createMock(\Magento\Webapi\Controller\Soap\Request\Handler::class);
        $mockProceed = function() { };

        $this->mockRequestFactory
            ->expects($this->never())
            ->method('create');

        $this->mockRegistry
            ->expects($this->once())
            ->method('registry')
            ->with('request_id')
            ->willReturn(1);

        $this->plugin->around__call(
            $mockSoapRequestHandler,
            $mockProceed,
            ShipmentImport::OP_SHIPMENT_IMPORT,
            ['data']
        );
    }
}
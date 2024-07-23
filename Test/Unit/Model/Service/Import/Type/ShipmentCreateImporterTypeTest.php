<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Service\Import\Type;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;
use Psr\Log\LoggerInterface;
use RealtimeDespatch\OrderFlow\Model\Service\Import\Type\ImporterType;
use RealtimeDespatch\OrderFlow\Model\Service\Import\Type\InventoryImporterType;
use RealtimeDespatch\OrderFlow\Helper\Stock as StockHelper;
use RealtimeDespatch\OrderFlow\Model\Service\Import\Type\ShipmentCreateImporterType;
use \RealtimeDespatch\OrderFlow\Model\Service\ShipmentService;

class ShipmentCreateImporterTypeTest extends AbstractImporterTypeTest
{
    protected ShipmentService $mockShipmentService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockShipmentService = $this->createMock(ShipmentService::class);

        $this->type = new ShipmentCreateImporterType(
            $this->mockScopeConfig,
            $this->mockLogger,
            $this->mockObjectManager,
            $this->mockShipmentService
        );
    }

    protected function getTestImportRequestLineBody(): object
    {
        return (object) [
            'orderIncrementId' => '100000001',
        ];
    }

    protected function getTypeName(): string
    {
        return 'Shipment';
    }

    protected function getEnabledXmlPath(): string
    {
        return 'orderflow_inventory_import/settings/is_enabled';
    }
}
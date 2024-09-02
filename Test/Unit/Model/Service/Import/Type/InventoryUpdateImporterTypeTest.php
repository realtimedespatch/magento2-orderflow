<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Service\Import\Type;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;
use Psr\Log\LoggerInterface;
use RealtimeDespatch\OrderFlow\Helper\StockHelperFactory;
use RealtimeDespatch\OrderFlow\Model\Service\Import\Type\ImporterType;
use RealtimeDespatch\OrderFlow\Model\Service\Import\Type\InventoryUpdateImporterType;
use RealtimeDespatch\OrderFlow\Helper\Stock as StockHelper;

class InventoryUpdateImporterTypeTest extends AbstractImporterTypeTest
{
    protected StockHelper\MsiStockHelper $mockMsiStockHelper;
    protected StockHelper\LegacyStockHelper $mockLegacyStockHelper;
    protected StockHelperFactory $mockStockHelperFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockStockHelperFactory = $this->createMock(StockHelperFactory::class);

        $this->type = new InventoryUpdateImporterType(
            $this->mockScopeConfig,
            $this->mockLogger,
            $this->mockObjectManager,
            $this->mockStockHelperFactory
        );
    }

    protected function getTestImportRequestLineBody(): object
    {
        return (object) [
            'sku' => 'ABC123',
            'qty' => 10,
            'source' => 'default',
        ];
    }

    protected function getTypeName(): string
    {
        return 'Inventory';
    }

    protected function getEnabledXmlPath(): string
    {
        return 'orderflow_inventory_import/settings/is_enabled';
    }
}
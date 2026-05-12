<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Service\Import\Type;

use RealtimeDespatch\OrderFlow\Helper\StockHelperFactory;
use RealtimeDespatch\OrderFlow\Helper\Stock as StockHelper;
use RealtimeDespatch\OrderFlow\Model\Indexer\ProductReindexer;
use RealtimeDespatch\OrderFlow\Model\Service\Import\Type\InventoryUpdateImporterType;

class InventoryUpdateImporterTypeTest extends AbstractImporterTypeTest
{
    protected StockHelper\MsiStockHelper $mockStockHelper;
    protected StockHelperFactory $mockStockHelperFactory;
    protected ProductReindexer $mockProductReindexer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockStockHelper = $this->createMock(StockHelper\MsiStockHelper::class);
        $this->mockStockHelperFactory = $this->createMock(StockHelperFactory::class);
        $this->mockProductReindexer = $this->createMock(ProductReindexer::class);

        $this->mockStockHelperFactory
            ->method('create')
            ->willReturn($this->mockStockHelper);

        $this->type = new InventoryUpdateImporterType(
            $this->mockScopeConfig,
            $this->mockLogger,
            $this->mockObjectManager,
            $this->mockProductReindexer,
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

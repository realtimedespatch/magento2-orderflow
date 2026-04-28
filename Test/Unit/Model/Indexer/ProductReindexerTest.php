<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Indexer;

use Magento\Catalog\Model\ResourceModel\Product as ProductResourceModel;
use Magento\Framework\Indexer\IndexerInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Module\Manager;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Model\Indexer\ProductReindexer;

class ProductReindexerTest extends TestCase
{
    private Manager $moduleManager;
    private IndexerRegistry $indexerRegistry;
    private ProductResourceModel $productResource;
    private ProductReindexer $reindexer;

    protected function setUp(): void
    {
        $this->moduleManager = $this->createMock(Manager::class);
        $this->indexerRegistry = $this->createMock(IndexerRegistry::class);
        $this->productResource = $this->createMock(ProductResourceModel::class);

        $this->reindexer = new ProductReindexer(
            $this->moduleManager,
            $this->indexerRegistry,
            $this->productResource
        );
    }

    public function testReindexSkusReturnsEarlyForEmptySkuList(): void
    {
        $this->moduleManager->expects($this->never())->method('isEnabled');
        $this->productResource->expects($this->never())->method('getProductsIdsBySkus');

        $this->reindexer->reindexSkus([]);
    }

    public function testReindexSkusReturnsEarlyWhenMsiIsDisabled(): void
    {
        $this->moduleManager
            ->expects($this->once())
            ->method('isEnabled')
            ->with('Magento_InventoryApi')
            ->willReturn(false);

        $this->productResource->expects($this->never())->method('getProductsIdsBySkus');

        $this->reindexer->reindexSkus(['ABC123']);
    }

    public function testReindexSkusReturnsEarlyWhenNoProductsAreResolved(): void
    {
        $this->moduleManager
            ->method('isEnabled')
            ->willReturn(true);

        $this->productResource
            ->expects($this->once())
            ->method('getProductsIdsBySkus')
            ->with(['ABC123'])
            ->willReturn([]);

        $this->indexerRegistry->expects($this->never())->method('get');

        $this->reindexer->reindexSkus(['ABC123']);
    }

    public function testReindexSkusReindexesResolvedIdsAcrossAllIndexers(): void
    {
        $catalogInventoryIndexer = $this->createMock(IndexerInterface::class);
        $catalogPriceIndexer = $this->createMock(IndexerInterface::class);
        $catalogSearchIndexer = $this->createMock(IndexerInterface::class);

        $this->moduleManager
            ->method('isEnabled')
            ->willReturn(true);

        $this->productResource
            ->expects($this->once())
            ->method('getProductsIdsBySkus')
            ->with(['ABC123', 'DEF456'])
            ->willReturn([
                'ABC123' => '10',
                'DEF456' => 20,
            ]);

        $this->indexerRegistry
            ->expects($this->exactly(3))
            ->method('get')
            ->willReturnMap([
                ['cataloginventory_stock', $catalogInventoryIndexer],
                ['catalog_product_price', $catalogPriceIndexer],
                ['catalogsearch_fulltext', $catalogSearchIndexer],
            ]);

        $catalogInventoryIndexer
            ->expects($this->once())
            ->method('reindexList')
            ->with([10, 20]);

        $catalogPriceIndexer
            ->expects($this->once())
            ->method('reindexList')
            ->with([10, 20]);

        $catalogSearchIndexer
            ->expects($this->once())
            ->method('reindexList')
            ->with([10, 20]);

        $this->reindexer->reindexSkus(['ABC123', 'ABC123', 'DEF456']);
    }
}

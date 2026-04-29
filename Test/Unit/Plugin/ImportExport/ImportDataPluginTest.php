<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Plugin\ImportExport;

use Magento\CatalogImportExport\Model\Import\Product as ProductImport;
use Magento\ImportExport\Model\ResourceModel\Import\Data as ImportDataResource;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Model\Product\ExportStatus\ProductExportStatusResolver;
use RealtimeDespatch\OrderFlow\Plugin\ImportExport\ImportDataPlugin;

class ImportDataPluginTest extends TestCase
{
    private const ENTITY_TYPE_CODE = 'catalog_product';

    private ProductExportStatusResolver $productExportStatusResolver;
    private ImportDataPlugin $plugin;
    private ImportDataResource $importDataResource;

    protected function setUp(): void
    {
        $this->productExportStatusResolver = $this->createMock(ProductExportStatusResolver::class);
        $this->plugin = new ImportDataPlugin($this->productExportStatusResolver);
        $this->importDataResource = $this->createMock(ImportDataResource::class);
    }

    public function testAfterGetNextUniqueBunchSkipsNonProductImports(): void
    {
        $bunch = [[ProductImport::COL_SKU => 'ABC']];
        $ids = [11, 12];

        $this->importDataResource
            ->expects($this->once())
            ->method('getEntityTypeCode')
            ->with($ids)
            ->willReturn('customer_main');

        $this->productExportStatusResolver
            ->expects($this->never())
            ->method('getSkusToSetPending');

        $this->assertSame($bunch, $this->plugin->afterGetNextUniqueBunch($this->importDataResource, $bunch, $ids));
    }

    public function testAfterGetNextUniqueBunchAddsPendingForEligibleProducts(): void
    {
        $bunch = [
            [ProductImport::COL_SKU => 'ABC', 'name' => 'Updated Product'],
            ['description' => 'Continuation row'],
            [ProductImport::COL_SKU => 'XYZ', 'orderflow_export_status' => 'Queued'],
            [ProductImport::COL_SKU => 'NEW-SKU', 'price' => '9.99'],
        ];
        $ids = [21, 22];

        $this->importDataResource
            ->expects($this->once())
            ->method('getEntityTypeCode')
            ->with($ids)
            ->willReturn(self::ENTITY_TYPE_CODE);

        $this->productExportStatusResolver
            ->expects($this->once())
            ->method('getSkusToSetPending')
            ->with(['ABC', 'NEW-SKU'])
            ->willReturn(['ABC', 'NEW-SKU']);

        $result = $this->plugin->afterGetNextUniqueBunch($this->importDataResource, $bunch, $ids);

        $this->assertSame(ProductExportStatusResolver::STATUS_PENDING, $result[0]['orderflow_export_status']);
        $this->assertSame(ProductExportStatusResolver::STATUS_PENDING, $result[1]['orderflow_export_status']);
        $this->assertSame('Queued', $result[2]['orderflow_export_status']);
        $this->assertSame(ProductExportStatusResolver::STATUS_PENDING, $result[3]['orderflow_export_status']);
    }

    public function testAfterGetNextUniqueBunchSkipsWhenNoEligibleProductsRemain(): void
    {
        $bunch = [
            [ProductImport::COL_SKU => 'ABC'],
        ];
        $ids = [31];

        $this->importDataResource
            ->expects($this->once())
            ->method('getEntityTypeCode')
            ->with($ids)
            ->willReturn(self::ENTITY_TYPE_CODE);

        $this->productExportStatusResolver
            ->expects($this->once())
            ->method('getSkusToSetPending')
            ->with(['ABC'])
            ->willReturn([]);

        $this->assertSame($bunch, $this->plugin->afterGetNextUniqueBunch($this->importDataResource, $bunch, $ids));
    }

    public function testAfterGetNextBunchDelegatesToSameLogic(): void
    {
        $bunch = [
            [ProductImport::COL_SKU => 'ABC'],
        ];

        $this->importDataResource
            ->expects($this->once())
            ->method('getEntityTypeCode')
            ->with(null)
            ->willReturn(self::ENTITY_TYPE_CODE);

        $this->productExportStatusResolver
            ->expects($this->once())
            ->method('getSkusToSetPending')
            ->with(['ABC'])
            ->willReturn(['ABC']);

        $result = $this->plugin->afterGetNextBunch($this->importDataResource, $bunch);

        $this->assertSame(ProductExportStatusResolver::STATUS_PENDING, $result[0]['orderflow_export_status']);
    }
}

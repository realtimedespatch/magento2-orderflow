<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Plugin\Catalog;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use RealtimeDespatch\OrderFlow\Model\Product\ExportStatus\ProductExportStatusResolver;
use RealtimeDespatch\OrderFlow\Plugin\Catalog\ProductSave;

class ProductSaveTest extends \PHPUnit\Framework\TestCase
{
    protected ProductSave $plugin;
    protected Product $mockProduct;
    protected ProductResource $mockProductResource;
    protected ProductExportStatusResolver $productExportStatusResolver;

    protected function setUp(): void
    {
        $this->productExportStatusResolver = $this->createMock(ProductExportStatusResolver::class);
        $this->plugin = new ProductSave($this->productExportStatusResolver);

        $this->mockProduct = $this->getMockBuilder(\Magento\Catalog\Model\Product::class)
            ->disableOriginalConstructor()
            ->addMethods(['setOrderflowExportStatus'])
            ->onlyMethods(['isDataChanged', 'dataHasChangedFor', 'getData'])
            ->getMock();

        $this->mockProductResource = $this->createMock(\Magento\Catalog\Model\ResourceModel\Product::class);
    }

    public function testBeforeSaveNoChanges(): void
    {
        $this->mockProduct
            ->expects($this->once())
            ->method('isDataChanged')
            ->willReturn(false);

        $this->productExportStatusResolver
            ->expects($this->never())
            ->method('shouldSetPending');

        $this->mockProduct
            ->expects($this->never())
            ->method('setOrderflowExportStatus');

        $result = $this->plugin->beforeSave($this->mockProductResource, $this->mockProduct);
        $this->assertEquals($this->mockProduct, $result[0]);
    }

    public function testBeforeSaveWithChanges(): void
    {
        $this->mockProduct
            ->expects($this->once())
            ->method('isDataChanged')
            ->willReturn(true);

        $this->mockProduct
            ->expects($this->once())
            ->method('getData')
            ->with('orderflow_export_status')
            ->willReturn('Queued');

        $this->mockProduct
            ->expects($this->once())
            ->method('dataHasChangedFor')
            ->with('orderflow_export_status')
            ->willReturn(false);

        $this->productExportStatusResolver
            ->expects($this->once())
            ->method('shouldSetPending')
            ->with('Queued', false)
            ->willReturn(true);

        $this->mockProduct
            ->expects($this->once())
            ->method('setOrderflowExportStatus')
            ->with('Pending');

        $result = $this->plugin->beforeSave($this->mockProductResource, $this->mockProduct);
        $this->assertEquals($this->mockProduct, $result[0]);
    }

    public function testBeforeSaveDisabledStatusPreserved(): void
    {
        $this->mockProduct
            ->expects($this->once())
            ->method('isDataChanged')
            ->willReturn(true);

        $this->mockProduct
            ->expects($this->once())
            ->method('getData')
            ->with('orderflow_export_status')
            ->willReturn('Disabled');

        $this->mockProduct
            ->expects($this->once())
            ->method('dataHasChangedFor')
            ->with('orderflow_export_status')
            ->willReturn(false);

        $this->productExportStatusResolver
            ->expects($this->once())
            ->method('shouldSetPending')
            ->with('Disabled', false)
            ->willReturn(false);

        $this->mockProduct
            ->expects($this->never())
            ->method('setOrderflowExportStatus');

        $result = $this->plugin->beforeSave($this->mockProductResource, $this->mockProduct);
        $this->assertEquals($this->mockProduct, $result[0]);
    }

    public function testBeforeSaveOrderflowStatusAlreadyChanged(): void
    {
        $this->mockProduct
            ->expects($this->once())
            ->method('isDataChanged')
            ->willReturn(true);

        $this->mockProduct
            ->expects($this->once())
            ->method('dataHasChangedFor')
            ->with('orderflow_export_status')
            ->willReturn(true);

        $this->mockProduct
            ->expects($this->once())
            ->method('getData')
            ->with('orderflow_export_status')
            ->willReturn('Queued');

        $this->productExportStatusResolver
            ->expects($this->once())
            ->method('shouldSetPending')
            ->with('Queued', true)
            ->willReturn(false);

        $this->mockProduct
            ->expects($this->never())
            ->method('setOrderflowExportStatus');

        $result = $this->plugin->beforeSave($this->mockProductResource, $this->mockProduct);
        $this->assertEquals($this->mockProduct, $result[0]);
    }
}

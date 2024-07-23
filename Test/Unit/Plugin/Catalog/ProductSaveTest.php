<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Plugin\Catalog;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use RealtimeDespatch\OrderFlow\Plugin\Catalog\ProductSave;

class ProductSaveTest extends \PHPUnit\Framework\TestCase
{
    protected ProductSave $plugin;
    protected Product $mockProduct;
    protected ProductResource $mockProductResource;

    protected function setUp(): void
    {
        $this->plugin = new ProductSave();

        $this->mockProduct = $this->getMockBuilder(\Magento\Catalog\Model\Product::class)
            ->disableOriginalConstructor()
            ->addMethods(['setOrderflowExportStatus'])
            ->onlyMethods(['isDataChanged', 'dataHasChangedFor'])
            ->getMock();

        $this->mockProductResource = $this->createMock(\Magento\Catalog\Model\ResourceModel\Product::class);
    }

    public function testBeforeSaveNoChanges(): void
    {
        $this->mockProduct
            ->expects($this->once())
            ->method('isDataChanged')
            ->willReturn(false);

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
            ->method('setOrderflowExportStatus')
            ->with('Pending');

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
            ->expects($this->never())
            ->method('setOrderflowExportStatus');

        $result = $this->plugin->beforeSave($this->mockProductResource, $this->mockProduct);
        $this->assertEquals($this->mockProduct, $result[0]);
    }
}
<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Plugin\Catalog;

use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Plugin\Catalog\ProductSave;
use Magento\Catalog\Model\ResourceModel\Product as ProductResourceModel;
use Magento\Catalog\Model\Product;
use RealtimeDespatch\OrderFlow\Model\Source\Export\Status as ExportStatus;

class ProductSaveTest extends TestCase
{
    /**
     * @var ProductSave
     */
    protected $plugin;

    public function setUp()
    {
        $this->plugin = new ProductSave();
    }

    /**
     * @dataProvider dataProviderBeforeSave
     */
    public function testBeforeSave(
        $isDataChanged,
        $isExportStatusChanged,
        $setDataCalledNumTimes
    ) {
        $productResourceModel = $this->getMockBuilder(ProductResourceModel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $product = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();

        $product->expects($this->atMost(1))
            ->method('isDataChanged')
            ->willReturn($isDataChanged);

        $product->expects($this->any())
            ->method('dataHasChangedFor')
            ->with(ProductSave::EXPORT_STATUS_KEY)
            ->willReturn($isExportStatusChanged);

        $product->expects($this->exactly($setDataCalledNumTimes))
            ->method('setData')
            ->with(ProductSave::EXPORT_STATUS_KEY, ExportStatus::STATUS_PENDING);

        $result = $this->plugin->beforeSave($productResourceModel, $product);
        $updatedProduct = $result[0];

        $this->assertSame($product, $updatedProduct);
    }

    public function dataProviderBeforeSave()
    {
        return [
            [true, true, 0],
            [false, false, 0],
            [false, true, 0],
            [true, false, 1],
        ];
    }
}

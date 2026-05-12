<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Service\Export\Type;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Stdlib\DateTime\DateTime;
use RealtimeDespatch\OrderFlow\Model\Service\Export\Type\ProductExportExporterType;
use RealtimeDespatch\OrderFlow\Helper\Export\Product as ProductHelper;

class ProductExportExporterTypeTest extends AbstractExporterTypeTest
{
    protected ProductRepositoryInterface $mockProductRepository;
    protected ProductHelper $mockProductHelper;
    protected Product $mockProduct;
    protected DateTime $mockDate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockProductRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->mockProductHelper = $this->createMock(ProductHelper::class);
        $this->mockProduct = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->addMethods(['setOrderflowExportStatus', 'setOrderflowExportDate'])
            ->getMock();
        $this->mockDate = $this->createMock(DateTime::class);

        $this->mockProductRepository
            ->method('get')
            ->willReturn($this->mockProduct);

        $this->mockProductHelper
            ->method('isProductExportEnabledForProductWebsites')
            ->willReturn(true);

        $this->exporterType = new ProductExportExporterType(
            $this->mockScopeConfig,
            $this->mockLogger,
            $this->mockObjectManager,
            $this->mockProductRepository,
            $this->mockProductHelper,
            $this->mockDate
        );
    }

    protected function getTestExportRequestLineBody(): object
    {
        return (object) [
            'sku' => 'test-sku'
        ];
    }

    protected function getEnabledConfigPath() : string
    {
        return 'orderflow_product_export/settings/is_enabled';
    }

    protected function getTypeName() : string
    {
        return 'Product';
    }
}

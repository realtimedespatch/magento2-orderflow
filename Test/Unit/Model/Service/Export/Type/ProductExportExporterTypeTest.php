<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Service\Export\Type;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\OrderRepository;
use RealtimeDespatch\OrderFlow\Model\Factory\OrderFlow\Service\OrderServiceFactory;
use RealtimeDespatch\OrderFlow\Model\Service\Export\Type\OrderCancelExporterType;
use RealtimeDespatch\OrderFlow\Model\Service\Export\Type\OrderCreateExporterType;
use RealtimeDespatch\OrderFlow\Model\Service\Export\Type\OrderExportExporterType;
use RealtimeDespatch\OrderFlow\Model\Service\Export\Type\ProductExportExporterType;
use RealtimeDespatch\OrderFlow\Helper\Export\Product as ProductHelper;

class ProductExportExporterTypeTest extends AbstractExporterTypeTest
{
    protected ProductRepositoryInterface $mockProductRepository;
    protected ProductHelper $mockProductHelper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockProductRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->mockProductHelper = $this->createMock(ProductHelper::class);

        $this->exporterType = new ProductExportExporterType(
            $this->mockScopeConfig,
            $this->mockLogger,
            $this->mockObjectManager,
            $this->mockProductRepository,
            $this->mockProductHelper
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
<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Service\Export\Type;

use Magento\Catalog\Api\ProductRepositoryInterface;
use RealtimeDespatch\OrderFlow\Model\Factory\OrderFlow\Service\ProductServiceFactory;
use RealtimeDespatch\OrderFlow\Model\Service\Export\Type\ProductUpdateExporterType;
use RealtimeDespatch\OrderFlow\Helper\Export\Product as ProductHelper;
use SixBySix\RealtimeDespatch\Service\ProductService;

class ProductUpdateExporterTypeTest extends AbstractExporterTypeTest
{
    protected ProductRepositoryInterface $mockProductRepository;
    protected ProductServiceFactory $mockProductServiceFactory;
    protected ProductHelper $mockProductHelper;
    protected ProductService $mockProductService;


    protected function setUp(): void
    {
        parent::setUp();

        $this->mockProductRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->mockProductHelper = $this->createMock(ProductHelper::class);
        $this->mockProductServiceFactory = $this->createMock(ProductServiceFactory::class);
        $this->mockProductService = $this->createMock(\SixBySix\RealtimeDespatch\Service\ProductService::class);

        $this->mockProductServiceFactory
            ->method('getService')
            ->willReturn($this->mockProductService);

        $this->exporterType = new ProductUpdateExporterType(
            $this->mockScopeConfig,
            $this->mockLogger,
            $this->mockObjectManager,
            $this->mockProductServiceFactory
        );
    }

    protected function preExportWithException(): void
    {
        $this->mockProductService
            ->method('notifyProductUpdate')
            ->willThrowException(new \Exception('Test Exception'));
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
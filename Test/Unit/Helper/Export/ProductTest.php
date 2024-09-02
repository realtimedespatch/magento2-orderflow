<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Helper\Export;

use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\WebsiteFactory;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Helper\Api;
use RealtimeDespatch\OrderFlow\Helper\Export\Product;
use Magento\Store\Model\ResourceModel\Website\CollectionFactory as WebsiteCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;

class ProductTest extends TestCase
{
    protected Product $productHelper;
    protected ScopeConfigInterface $mockScopeConfig;
    protected ProductFactory $mockProductFactory;
    protected WebsiteCollectionFactory $mockWebsiteFactory;
    protected ProductRepository $mockProductRepository;
    protected ProductCollectionFactory $mockProductCollectionFactory;
    protected Api $mockApiHelper;

    protected function setUp(): void
    {
        $this->mockScopeConfig = $this->createMock(ScopeConfigInterface::class);

        $mockContext = $this->createMock(\Magento\Framework\App\Helper\Context::class);
        $mockContext->method('getScopeConfig')->willReturn($this->mockScopeConfig);

        $this->mockWebsiteFactory = $this->createMock(WebsiteCollectionFactory::class);
        $this->mockProductCollectionFactory = $this->createMock(ProductCollectionFactory::class);
        $this->mockProductRepository = $this->createMock(ProductRepository::class);

        $this->mockApiHelper = $this->createMock(Api::class);

        $this->productHelper = new Product(
            $mockContext,
            $this->mockProductCollectionFactory,
            $this->mockWebsiteFactory,
            $this->mockProductRepository,
            $this->mockApiHelper,
        );

        parent::setUp();
    }

    /**
     * @dataProvider boolDataProvider
     */
    public function testIsEnabled($enabled): void
    {
        $this->mockScopeConfig
            ->method('isSetFlag')
            ->with(
                'orderflow_product_export/settings/is_enabled',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
            )
            ->willReturn($enabled);

        $this->assertEquals($enabled, $this->productHelper->isEnabled());
    }

    public function testGetBatchSize(): void
    {
        $batchSize = rand(1, 100);

        $this->mockScopeConfig
            ->method('getValue')
            ->with('orderflow_product_export/settings/batch_size')
            ->willReturn($batchSize);

        $this->assertEquals($batchSize, $this->productHelper->getBatchSize());
    }

    public function testGetStoreId()
    {
        $storeId = rand(1, 100);

        $this->mockScopeConfig
            ->method('getValue')
            ->with(
                'orderflow_product_export/settings/store_id',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            )
            ->willReturn($storeId);

        $this->assertEquals($storeId, $this->productHelper->getStoreId(rand(1, 100)));
    }

    /**
     * @return void
     */
    public function testGetCreateableProducts(): void
    {
        $websiteId = rand(1, 50);
        $storeId = rand(1, 50);
        $batchSize = rand(1, 100);

        $mockWebsite = $this->createMock(\Magento\Store\Model\Website::class);
        $mockWebsite->method('getId')->willReturn($websiteId);

        $mockProductCollection = $this->createMock(ProductCollection::class);
        $mockProductCollection
            ->method('addAttributeToFilter')
            ->willReturnSelf();

        $mockProductCollection
            ->expects($this->once())
            ->method('setStore')
            ->with($storeId)
            ->willReturnSelf();

        $mockProductCollection
            ->expects($this->once())
            ->method('setPage')
            ->with(1, $batchSize)
            ->willReturnSelf();

        $this->mockProductCollectionFactory
            ->method('create')
            ->willReturn($mockProductCollection);

        $this->mockScopeConfig
            ->method('getValue')
            ->withConsecutive(
                [
                    'orderflow_product_export/settings/store_id',
                    \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
                    $websiteId
                ],
                [
                    'orderflow_product_export/settings/batch_size',
                    \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
                    $websiteId,
                ]
            )
            ->willReturnOnConsecutiveCalls(
                $storeId,
                $batchSize,
            );

        $this->mockScopeConfig
            ->method('isSetFlag')
            ->with(
                'orderflow_product_export/settings/is_enabled',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
                $websiteId
            )
            ->willReturn(true);

        $this->mockWebsiteFactory
            ->method('create')
            ->willReturn([
                $mockWebsite
            ]);

        $this->mockApiHelper
            ->method('getEndpoint')
            ->with($websiteId)
            ->willReturn('endpoint');

        $this->mockApiHelper
            ->method('getOrganisation')
            ->with($websiteId)
            ->willReturn('organisation');

        $this->mockApiHelper
            ->method('getChannel')
            ->with($websiteId)
            ->willReturn('channel');

        $createableProducts = $this->productHelper->getCreateableProducts($mockWebsite);
        $this->assertInstanceOf(ProductCollection::class, $createableProducts);
    }

    public function testGetUpdateableProducts()
    {
        $websiteId = rand(1, 50);

        $mockWebsite = $this->createMock(\Magento\Store\Model\Website::class);

        $mockWebsite
            ->method('getId')
            ->willReturn($websiteId);

        $this->mockWebsiteFactory
            ->method('create')
            ->willReturn([
                $mockWebsite
            ]);

        $mockProductCollection = $this->createMock(ProductCollection::class);
        $mockProductCollection
            ->expects($this->exactly(2))
            ->method('addAttributeToFilter')
            ->withConsecutive(
                ['type_id', ['eq' => 'simple']],
                ['orderflow_export_date', ['notnull' => true]]
            )
            ->willReturnSelf();

        $mockProductCollection
            ->expects($this->once())
            ->method('addFieldToFilter')
            ->with('orderflow_export_status', ['eq' => 'Pending'])
            ->willReturnSelf();

        $mockProductCollection
            ->expects($this->once())
            ->method('setStore')
            ->willReturnSelf();

        $mockProductCollection
            ->expects($this->once())
            ->method('setPage')
            ->willReturnSelf();

        $this->mockProductCollectionFactory
            ->method('create')
            ->willReturn($mockProductCollection);

        $this->mockScopeConfig
            ->method('isSetFlag')
            ->with(
                'orderflow_product_export/settings/is_enabled',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
                $websiteId
            )
            ->willReturn(true);

        $this->mockWebsiteFactory
            ->method('create')
            ->willReturn([
                $mockWebsite
            ]);

        $this->mockApiHelper
            ->method('getEndpoint')
            ->with($websiteId)
            ->willReturn('endpoint');

        $this->mockApiHelper
            ->method('getOrganisation')
            ->with($websiteId)
            ->willReturn('organisation');

        $this->mockApiHelper
            ->method('getChannel')
            ->with($websiteId)
            ->willReturn('channel');

        $this->productHelper->getUpdateableProducts($mockWebsite);
    }

    public function testIsProductExportEnabledForProductWebsites()
    {
        $websiteId = rand(1, 50);
        $storeId = rand(1, 50);

        $mockWebsite = $this->createMock(\Magento\Store\Model\Website::class);
        $mockWebsite->method('getId')->willReturn($websiteId);

        $this->mockWebsiteFactory
            ->method('create')
            ->willReturn([
                $mockWebsite
            ]);

        $mockProduct = $this->createMock(\Magento\Catalog\Model\Product::class);
        $mockProduct->method('getWebsiteIds')->willReturn([$websiteId]);

        $this->mockScopeConfig
            ->method('isSetFlag')
            ->with(
                'orderflow_product_export/settings/is_enabled',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
                $websiteId
            )
            ->willReturn(true);

        $this->mockScopeConfig
            ->method('getValue')
            ->with(
                'orderflow_product_export/settings/store_id',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
                $websiteId
            )
            ->willReturn($storeId);

        $this->mockProductRepository
            ->method('get')
            ->with('sku', false, \Magento\Store\Model\Store::DEFAULT_STORE_ID)
            ->willReturn($mockProduct);

        $this->assertTrue($this->productHelper->isProductExportEnabledForProductWebsites($mockProduct));
        $this->assertTrue($this->productHelper->isProductExportEnabledForProductWebsites("sku"));
    }

    public function boolDataProvider(): array
    {
        return [
            [true],
            [false],
        ];
    }
}
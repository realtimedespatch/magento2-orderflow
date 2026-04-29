<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Product\ExportStatus;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Model\Product\ExportStatus\ProductExportStatusResolver;

class ProductExportStatusResolverTest extends TestCase
{
    private ProductExportStatusResolver $resolver;
    private CollectionFactory $productCollectionFactory;
    private Collection $productCollection;

    protected function setUp(): void
    {
        $this->productCollectionFactory = $this->createMock(CollectionFactory::class);
        $this->productCollection = $this->createMock(Collection::class);

        $this->resolver = new ProductExportStatusResolver($this->productCollectionFactory);
    }

    public function testShouldSetPending(): void
    {
        $this->assertTrue($this->resolver->shouldSetPending('Queued', false));
        $this->assertTrue($this->resolver->shouldSetPending(null, false));
        $this->assertFalse($this->resolver->shouldSetPending('Disabled', false));
        $this->assertFalse($this->resolver->shouldSetPending('Queued', true));
    }

    public function testGetProductIdsToSetPendingFiltersDisabledStatuses(): void
    {
        $enabledProduct = $this->createConfiguredMock(Product::class, [
            'getData' => 'Queued',
            'getId' => 10,
        ]);
        $disabledProduct = $this->createConfiguredMock(Product::class, [
            'getData' => 'Disabled',
            'getId' => 11,
        ]);

        $this->productCollectionFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->productCollection);

        $this->productCollection
            ->expects($this->once())
            ->method('addIdFilter')
            ->with([10, 11, 12])
            ->willReturnSelf();

        $this->productCollection
            ->expects($this->once())
            ->method('addAttributeToSelect')
            ->with('orderflow_export_status')
            ->willReturnSelf();

        $this->productCollection
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([$enabledProduct, $disabledProduct]));

        $this->assertSame([10], $this->resolver->getProductIdsToSetPending([10, 11, 12, '10']));
    }

    public function testGetProductIdsBySkusToSetPendingFiltersDisabledStatuses(): void
    {
        $enabledProduct = $this->createConfiguredMock(Product::class, [
            'getData' => null,
            'getId' => 21,
        ]);
        $disabledProduct = $this->createConfiguredMock(Product::class, [
            'getData' => 'Disabled',
            'getId' => 22,
        ]);

        $this->productCollectionFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->productCollection);

        $this->productCollection
            ->expects($this->once())
            ->method('addAttributeToSelect')
            ->with('orderflow_export_status')
            ->willReturnSelf();

        $this->productCollection
            ->expects($this->once())
            ->method('addAttributeToFilter')
            ->with('sku', ['in' => ['ABC', 'XYZ']])
            ->willReturnSelf();

        $this->productCollection
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([$enabledProduct, $disabledProduct]));

        $this->assertSame([21], $this->resolver->getProductIdsBySkusToSetPending(['ABC', 'XYZ', 'ABC', '']));
    }

    public function testGetSkusToSetPendingKeepsNewSkusAndRemovesDisabledOnes(): void
    {
        $enabledProduct = $this->createConfiguredMock(Product::class, [
            'getData' => 'Queued',
            'getSku' => 'ABC',
        ]);
        $disabledProduct = $this->createConfiguredMock(Product::class, [
            'getData' => 'Disabled',
            'getSku' => 'XYZ',
        ]);

        $this->productCollectionFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->productCollection);

        $this->productCollection
            ->expects($this->once())
            ->method('addAttributeToSelect')
            ->with('orderflow_export_status')
            ->willReturnSelf();

        $this->productCollection
            ->expects($this->once())
            ->method('addAttributeToFilter')
            ->with('sku', ['in' => ['ABC', 'XYZ', 'NEW']])
            ->willReturnSelf();

        $this->productCollection
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([$enabledProduct, $disabledProduct]));

        $this->assertSame(['ABC', 'NEW'], $this->resolver->getSkusToSetPending(['ABC', 'XYZ', 'NEW']));
    }
}

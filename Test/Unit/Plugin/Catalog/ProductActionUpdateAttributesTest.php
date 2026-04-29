<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Plugin\Catalog;

use Magento\Catalog\Model\Product\Action as ProductAction;
use Magento\Catalog\Model\ResourceModel\Product\Action as ProductActionResource;
use Magento\Store\Model\Store;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Model\Product\ExportStatus\ProductExportStatusResolver;
use RealtimeDespatch\OrderFlow\Plugin\Catalog\ProductActionUpdateAttributes;

class ProductActionUpdateAttributesTest extends TestCase
{
    private ProductExportStatusResolver $productExportStatusResolver;
    private ProductActionResource $productActionResource;
    private ProductActionUpdateAttributes $plugin;
    private ProductAction $subject;

    protected function setUp(): void
    {
        $this->productExportStatusResolver = $this->createMock(ProductExportStatusResolver::class);
        $this->productActionResource = $this->createMock(ProductActionResource::class);
        $this->subject = $this->createMock(ProductAction::class);

        $this->plugin = new ProductActionUpdateAttributes(
            $this->productExportStatusResolver,
            $this->productActionResource
        );
    }

    public function testAroundUpdateAttributesSkipsWhenStatusIsExplicitlyUpdated(): void
    {
        $proceedCalls = 0;
        $proceed = function ($productIds, $attrData, $storeId) use (&$proceedCalls) {
            $proceedCalls++;
            return 'result';
        };

        $this->productExportStatusResolver
            ->expects($this->never())
            ->method('getProductIdsToSetPending');

        $this->productActionResource
            ->expects($this->never())
            ->method('updateAttributes');

        $result = $this->plugin->aroundUpdateAttributes(
            $this->subject,
            $proceed,
            [10, 11],
            ['orderflow_export_status' => 'Queued'],
            2
        );

        $this->assertSame('result', $result);
        $this->assertSame(1, $proceedCalls);
    }

    public function testAroundUpdateAttributesFlagsEligibleProductsPending(): void
    {
        $proceedCalls = 0;
        $proceed = function ($productIds, $attrData, $storeId) use (&$proceedCalls) {
            $proceedCalls++;
            return 'result';
        };

        $this->productExportStatusResolver
            ->expects($this->once())
            ->method('getProductIdsToSetPending')
            ->with([10, 11, 12])
            ->willReturn([10, 12]);

        $this->productActionResource
            ->expects($this->once())
            ->method('updateAttributes')
            ->with(
                [10, 12],
                ['orderflow_export_status' => ProductExportStatusResolver::STATUS_PENDING],
                Store::DEFAULT_STORE_ID
            );

        $result = $this->plugin->aroundUpdateAttributes(
            $this->subject,
            $proceed,
            [10, 11, 12],
            ['name' => 'Updated Name'],
            2
        );

        $this->assertSame('result', $result);
        $this->assertSame(1, $proceedCalls);
    }

    public function testAroundUpdateAttributesSkipsWhenNoEligibleProductsRemain(): void
    {
        $proceed = fn ($productIds, $attrData, $storeId) => 'result';

        $this->productExportStatusResolver
            ->expects($this->once())
            ->method('getProductIdsToSetPending')
            ->with([10, 11])
            ->willReturn([]);

        $this->productActionResource
            ->expects($this->never())
            ->method('updateAttributes');

        $result = $this->plugin->aroundUpdateAttributes(
            $this->subject,
            $proceed,
            [10, 11],
            ['visibility' => 4],
            0
        );

        $this->assertSame('result', $result);
    }
}

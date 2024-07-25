<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Cron\Export;

use Magento\Catalog\Model\Product;
use Magento\Framework\Model\AbstractModel;
use Magento\Sales\Model\Order;
use RealtimeDespatch\OrderFlow\Cron\Export\ProductCreateExport;
use RealtimeDespatch\OrderFlow\Cron\Export\ProductUpdateExport;
use RealtimeDespatch\OrderFlow\Helper\Export\Product as ExportProductHelper;

class ProductUpdateExportCronTest extends AbstractExportCronTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->mockExportHelper = $this->createMock(ExportProductHelper::class);

        $this->exportCron = new ProductUpdateExport(
            $this->mockExportHelper,
            $this->mockLogger,
            $this->mockRequestBuilder,
            $this->mockObjectManager,
            $this->mockWebsiteFactory
        );
    }

    /**
     * @dataProvider testExecuteDataProvider
     * @return void
     */
    public function testExecute(bool $isEnabled, int $numEntities = 1): void
    {
        $this->mockExportHelper
            ->method('isEnabled')
            ->with(1)
            ->willReturn($isEnabled);

        $this->mockExportHelper
            ->expects(($isEnabled) ? $this->once() : $this->never())
            ->method('getUpdateableProducts')
            ->with($this->mockWebsite)
            ->willReturn(
                $this->getExportableEntities($numEntities)
            );

        if ($isEnabled && $numEntities > 0) {
            $this->mockRequestBuilder
                ->expects($this->exactly($numEntities))
                ->method('setRequestData')
                ->with(
                    'Export',
                    'Product',
                    'Update'
                );

            $this->mockRequestBuilder
                ->expects($this->exactly($numEntities))
                ->method('addRequestLine')
                ->with(json_encode([
                    'sku' => 'TEST-SKU',
                ]));

            $this->mockRequest
                ->expects($this->once())
                ->method('getEntity')
                ->willReturn('Product');

            $this->mockRequest
                ->expects($this->once())
                ->method('getOperation')
                ->willReturn('Update');

            $this->mockObjectManager
                ->expects($this->once())
                ->method('create')
                ->with('ProductUpdateRequestProcessor')
                ->willReturn($this->mockRequestProcessor);
        }

        parent::testExecute($isEnabled, $numEntities);
    }

    protected function getMockEntity(): AbstractModel
    {
        $mockProduct = $this->createMock(Product::class);

        $mockProduct
            ->method('getEntityId')
            ->willReturn(1);

        $mockProduct
            ->method('getSku')
            ->willReturn('TEST-SKU');

        return $mockProduct;
    }
}
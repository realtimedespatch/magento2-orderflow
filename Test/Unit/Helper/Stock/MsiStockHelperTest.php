<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Helper\Stock;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Module\Manager;
use Magento\Inventory\Model\SourceItem;
use Magento\Quote\Model\QuoteFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use RealtimeDespatch\OrderFlow\Helper\Import\Inventory;
use RealtimeDespatch\OrderFlow\Helper\Stock\MsiStockHelper;

class MsiStockHelperTest extends AbstractStockHelperTest
{

    protected \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $mockSourceItemFactory;
    protected SourceItem $mockSourceItem;
    protected \Magento\InventoryApi\Api\SourceItemsSaveInterface $mockSourceItemsSave;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockSourceItemFactory = $this->createMock(\Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory::class);
        $this->mockSourceItemsSave = $this->createMock(\Magento\InventoryApi\Api\SourceItemsSaveInterface::class);
        $this->mockSourceItem = $this->createMock(SourceItem::class);

        $this->stockHelper = new MsiStockHelper(
            $this->mockContext,
            $this->mockProductRepository,
            $this->mockInventoryHelper,
            $this->mockOrderFactory,
            $this->mockQuoteFactory,
            $this->mockSourceItemFactory,
            $this->mockSourceItemsSave
        );
    }


    /**
     * @dataProvider testUpdateProductStockDataProvider
     * @param string $sku
     * @param int $inputQty
     * @param int $outputQty
     * @param int $inStock
     * @param \DateTime $date
     * @param bool $negativeQtyEnabled
     * @param string $sourceCode
     * @param bool $unsentOrderAdjustment
     * @param int $unsetOrderQty
     * @param bool $activeQuoteAdjustment
     * @param int $activeQuoteQty
     * @return void
     */
    public function testUpdateProductStock(
        string $sku,
        int $inputQty,
        int $outputQty,
        int $inStock,
        \DateTime $date,
        bool $negativeQtyEnabled,
        string $sourceCode,
        bool $unsentOrderAdjustment = false,
        int $unsetOrderQty = 0,
        bool $activeQuoteAdjustment = false,
        int $activeQuoteQty = 0
    ): void
    {
        $this->mockSourceItemFactory
            ->method('create')
            ->willReturn($this->mockSourceItem);

        $this->mockSourceItem
            ->expects($this->once())
            ->method('setSourceCode')
            ->with($sourceCode);

        $this->mockSourceItem
            ->expects($this->once())
            ->method('setSku')
            ->with($sku);

        $this->mockSourceItem
            ->expects($this->once())
            ->method('setQuantity')
            ->with($outputQty);

        $this->mockSourceItem
            ->expects($this->once())
            ->method('setStatus')
            ->with($inStock);

        $this->mockSourceItemsSave
            ->expects($this->once())
            ->method('execute')
            ->with([$this->mockSourceItem]);

        parent::testUpdateProductStock(
            $sku,
            $inputQty,
            $outputQty,
            $inStock,
            $date,
            $negativeQtyEnabled,
            $sourceCode,
            $unsentOrderAdjustment,
            $unsetOrderQty,
            $activeQuoteAdjustment,
            $activeQuoteQty
        );
    }

    public function testUpdateProductStockNegative(): void
    {
        $sku = 'SKU';
        $qty = -10;
        $lastOrderExported = new \DateTime();
        $source = 'default';

        $this->mockProductRepository
            ->method('get')
            ->willReturn($this->createMock(\Magento\Catalog\Api\Data\ProductInterface::class));

        $this->mockInventoryHelper
            ->method('isNegativeQtyEnabled')
            ->willReturn(false);

        $this->mockSourceItemFactory
            ->method('create')
            ->willReturn($this->mockSourceItem);

        $this->mockSourceItem
            ->expects($this->once())
            ->method('setSourceCode')
            ->with($source);

        $this->mockSourceItem
            ->expects($this->once())
            ->method('setSku')
            ->with($sku);

        $this->mockSourceItem
            ->expects($this->once())
            ->method('setQuantity')
            ->with(0);

        $this->mockSourceItem
            ->expects($this->once())
            ->method('setStatus')
            ->with(0);

        $this->mockSourceItemsSave
            ->expects($this->once())
            ->method('execute')
            ->with([$this->mockSourceItem]);

        $this->stockHelper->updateProductStock($sku, $qty, $lastOrderExported, $source);
    }
}
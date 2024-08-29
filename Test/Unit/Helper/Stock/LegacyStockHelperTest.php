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
use RealtimeDespatch\OrderFlow\Helper\Stock\LegacyStockHelper;
use RealtimeDespatch\OrderFlow\Helper\Stock\MsiStockHelper;

class LegacyStockHelperTest extends AbstractStockHelperTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->stockHelper = new LegacyStockHelper(
            $this->mockProductRepository,
            $this->mockInventoryHelper,
            $this->mockOrderFactory,
            $this->mockQuoteFactory,
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
        $this->mockProduct
            ->expects($this->once())
            ->method('setStockData');
            //->with(['qty' => $outputQty, 'is_in_stock' => $inStock]);

        $this->mockProduct
            ->expects($this->once())
            ->method('setQuantityAndStockStatus')
            ->with(['qty' => $outputQty, 'is_in_stock' => $inStock]);

        $this->mockProductRepository
            ->expects($this->once())
            ->method('save')
            ->with();

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
}
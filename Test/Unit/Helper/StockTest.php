<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Helper;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Module\Manager;
use Magento\Quote\Model\QuoteFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use RealtimeDespatch\OrderFlow\Helper\Api;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Helper\Import\Inventory;
use RealtimeDespatch\OrderFlow\Helper\Stock;

class StockTest extends TestCase
{
    protected Stock $stockHelper;
    protected ScopeConfigInterface $mockScopeConfig;
    protected Manager $mockModuleManager;
    protected ProductRepositoryInterface $mockProductRepository;
    protected Inventory $mockInventoryHelper;
    protected OrderFactory $mockOrderFactory;
    protected QuoteFactory $mockQuoteFactory;
    protected Order $mockOrder;

    protected function setUp(): void
    {
        $this->mockQuoteFactory = $this->createMock(\Magento\Quote\Model\QuoteFactory::class);
        $this->mockOrderFactory = $this->createMock(\Magento\Sales\Model\OrderFactory::class);
        $this->mockInventoryHelper = $this->createMock(\RealtimeDespatch\OrderFlow\Helper\Import\Inventory::class);
        $this->mockScopeConfig = $this->createMock(ScopeConfigInterface::class);
        $this->mockProductRepository = $this->createMock(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $this->mockModuleManager = $this->createMock(\Magento\Framework\Module\Manager::class);
        $this->mockOrder = $this->createMock(\Magento\Sales\Model\Order::class);

        $mockContext = $this->createMock(\Magento\Framework\App\Helper\Context::class);
        $mockContext->method('getScopeConfig')->willReturn($this->mockScopeConfig);
        $mockContext->method('getModuleManager')->willReturn($this->mockModuleManager);

        $this->stockHelper = new Stock(
            $mockContext,
            $this->mockProductRepository,
            $this->mockInventoryHelper,
            $this->createMock(\Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory::class),
            $this->createMock(\Magento\InventoryApi\Api\SourceItemsSaveInterface::class),
            $this->mockOrderFactory,
            $this->mockQuoteFactory,
            $this->mockModuleManager,
        );
    }

    public function testSetSourceItemFactory()
    {
        $mockSourceItemFactory = $this->createMock(\Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory::class);
        $this->stockHelper->setSourceItemFactory($mockSourceItemFactory);
    }

    public function testSetSourceItemsSave()
    {
        $mockSourceItemsSave = $this->createMock(\Magento\InventoryApi\Api\SourceItemsSaveInterface::class);
        $this->stockHelper->setSourceItemsSave($mockSourceItemsSave);
    }

    public function testUpdateProductStockMsi()
    {
        $mockProduct = $this->createMock(\Magento\Catalog\Api\Data\ProductInterface::class);
        $mockProduct->method('getSku')->willReturn('TEST-SKU');

        $this->mockProductRepository->expects($this->once())
            ->method('get')
            ->with('TEST-SKU')
            ->willReturn($mockProduct);

        $this->mockModuleManager->expects($this->once())
            ->method('isEnabled')
            ->with('Magento_InventoryApi')
            ->willReturn(true);

        $this->stockHelper->updateProductStock('TEST-SKU', 10, new \DateTime());
    }

    /**
     * @return void
     * @dataProvider testUpdateProductStockDataProvider
     * @param string $sku
     * @param int $inputQty
     * @param int $outputQty
     * @param int $inStock
     * @param \DateTime $date
     * @param bool $negativeQtyEnabled
     * @param string $sourceCode
     * @param bool $unsentOrderAdjustment
     * @param bool $activeQuoteAdjustment
     */
    public function testUpdateProductStockNonMsi(
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
        $mockProduct = $this->createMock(\Magento\Catalog\Model\Product::class);
        $mockProduct->method('getSku')->willReturn($sku);

        $mockProduct->expects($this->once())
            ->method('setStockData')
            ->with(['qty' => $outputQty, 'is_in_stock' => $inStock]);

        $mockProduct->expects($this->once())
            ->method('setQuantityAndStockStatus')
            ->with(['qty' => $outputQty, 'is_in_stock' => $inStock]);

        $mockProduct
            ->method('getId')
            ->willReturn(500);

        $this->mockInventoryHelper
            ->expects($this->once())
            ->method('isNegativeQtyEnabled')
            ->willReturn($negativeQtyEnabled);

        $this->mockInventoryHelper
            ->expects($this->once())
            ->method('isUnsentOrderAdjustmentEnabled')
            ->willReturn($unsentOrderAdjustment);

        $this->mockInventoryHelper
            ->expects($this->once())
            ->method('isActiveQuoteAdjustmentEnabled')
            ->willReturn($activeQuoteAdjustment);

        if ($unsentOrderAdjustment) {

            $this->mockInventoryHelper
                ->expects($this->once())
                ->method('getValidUnsentOrderStatuses')
                ->willReturn(['processing']);

            $this->mockOrderFactory->expects($this->once())
                ->method('create')
                ->willReturn($this->mockOrder);
            $mockOrderCollection = $this->createMock(\Magento\Sales\Model\ResourceModel\Order\Collection::class);
            $mockOrderSelect = $this->createMock(\Magento\Framework\DB\Select::class);

            $mockOrderCollection->expects($this->once())
                ->method('getSelect')
                ->willReturn($mockOrderSelect);

            $mockOrderSelect->expects($this->once())
                ->method('joinLeft')
                ->willReturnSelf();

            $mockOrderCollection->expects($this->once())
                ->method('addFieldToSelect')
                ->with('entity_id')
                ->willReturnSelf();

            $mockOrderCollection->expects($this->exactly(4))
                ->method('addFieldToFilter')
                ->willReturnSelf();

            $mockOrderCollection->expects($this->once())
                ->method('getData')
                ->willReturn([['qty' => $unsetOrderQty,]]);

            $this->mockOrder->expects($this->once())
                ->method('getCollection')
                ->willReturn($mockOrderCollection);
        }

        if ($activeQuoteAdjustment) {

            $mockQuote = $this->createMock(\Magento\Quote\Model\Quote::class);
            $mockQuoteCollection = $this->createMock(\Magento\Quote\Model\ResourceModel\Quote\Collection::class);
            $mockQuoteSelect = $this->createMock(\Magento\Framework\DB\Select::class);

            $this->mockQuoteFactory
                ->expects($this->once())
                ->method('create')
                ->willReturn($mockQuote);

            $mockQuote
                ->expects($this->once())
                ->method('getCollection')
                ->willReturn($mockQuoteCollection);

            $mockQuoteCollection
                ->expects($this->once())
                ->method('getSelect')
                ->willReturn($mockQuoteSelect);

            $mockQuoteSelect
                ->expects($this->once())
                ->method('joinLeft')
                ->willReturnSelf();

            $mockQuoteCollection
                ->expects($this->once())
                ->method('addFieldToSelect')
                ->with('entity_id')
                ->willReturnSelf();

            $mockQuoteCollection
                ->expects($this->exactly(3))
                ->method('addFieldToFilter')
                ->willReturnSelf();

            $mockQuoteCollection
                ->expects($this->once())
                ->method('getData')
                ->willReturn([['qty' => $activeQuoteQty,]]);
        }

        $this->mockInventoryHelper
            ->expects($this->once())
            ->method('isActiveQuoteAdjustmentEnabled')
            ->willReturn($activeQuoteAdjustment);

        $this->mockProductRepository->expects($this->once())
            ->method('get')
            ->with($sku)
            ->willReturn($mockProduct);

        $this->mockProductRepository->expects($this->once())
            ->method('save')
            ->with($mockProduct);

        $this->mockModuleManager->expects($this->once())
            ->method('isEnabled')
            ->with('Magento_InventoryApi')
            ->willReturn(false);

        $this->stockHelper->updateProductStock($sku, $inputQty, $date);
    }

    public function testUpdateProductStockDataProvider()
    {
        return [

            ['TEST-SKU', 10, 10, 1, new \DateTime(), false, 'default', false],
            ['TEST-SKU', 10, 10, 1, new \DateTime(), false, 'default', true, 0],
            ['TEST-SKU', 10, 6, 1, new \DateTime(), false, 'default', true, 4],
            ['TEST-SKU', 10, 4, 1, new \DateTime(), false, 'default', true, 4, true, 2],

            ['TEST-SKU', -10, 0, 0, new \DateTime(), false, 'default'],
            ['TEST-SKU', -10, 0, 0, new \DateTime(), false, 'default'],
            ['TEST-SKU', -10, -10, 0, new \DateTime(), true, 'default'],
            ['TEST-SKU', 10, 10, 1, new \DateTime(), false, 'warehouse'],
        ];
    }
}

<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Helper\Stock;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Module\Manager;
use Magento\Inventory\Model\Source;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\Inventory\Model\SourceItem;
use Magento\InventoryApi\Api\Data\StockSearchResultsInterface;
use Magento\InventoryApi\Api\GetSourcesAssignedToStockOrderedByPriorityInterface;
use Magento\InventoryApi\Api\StockRepositoryInterface;
use Magento\InventoryReservations\Model\ResourceModel\GetReservationsQuantity;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\Quote\Model\QuoteFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Helper\Import\Inventory;
use RealtimeDespatch\OrderFlow\Helper\Stock\MsiStockHelper;


class MsiStockHelperTest extends TestCase
{

    protected \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $mockSourceItemFactory;
    protected SourceItem $mockSourceItem;
    protected SourceInterface $mockSource;
    protected \Magento\InventoryApi\Api\SourceItemsSaveInterface $mockSourceItemsSave;
    protected SearchCriteriaBuilder $mockSearchCriteriaBuilder;
    protected \Magento\Framework\Api\SearchCriteria $mockSearchCriteria;
    protected StockResolverInterface $mockStockResolver;
    protected GetReservationsQuantity $mockGetReservationsQuantity;
    protected StockRepositoryInterface $mockStockRepository;
    protected GetSourcesAssignedToStockOrderedByPriorityInterface $mockSourcesAssignedToStock;
    protected ProductRepositoryInterface $mockProductRepository;
    protected Inventory $mockInventoryHelper;
    protected MsiStockHelper $stockHelper;
    protected StockSearchResultsInterface $mockStockSearchResults;
    protected \Magento\InventoryApi\Api\Data\StockInterface $mockStock;

    protected function setUp(): void
    {
        $this->mockSourceItemFactory = $this->createMock(\Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory::class);
        $this->mockSourceItemsSave = $this->createMock(\Magento\InventoryApi\Api\SourceItemsSaveInterface::class);
        $this->mockSourceItem = $this->createMock(SourceItem::class);
        $this->mockSearchCriteriaBuilder = $this->createMock(SearchCriteriaBuilder::class);
        $this->mockSearchCriteria = $this->createMock(\Magento\Framework\Api\SearchCriteria::class);
        $this->mockStockResolver = $this->createMock(StockResolverInterface::class);
        $this->mockGetReservationsQuantity = $this->createMock(GetReservationsQuantity::class);
        $this->mockStockRepository = $this->createMock(StockRepositoryInterface::class);
        $this->mockSourcesAssignedToStock = $this->createMock(GetSourcesAssignedToStockOrderedByPriorityInterface::class);
        $this->mockProductRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->mockInventoryHelper = $this->createMock(Inventory::class);
        $this->mockStockSearchResults = $this->createMock(StockSearchResultsInterface::class);
        $this->mockStock = $this->createMock(\Magento\InventoryApi\Api\Data\StockInterface::class);
        $this->mockSource = $this->createMock(SourceInterface::class);

        $this->stockHelper = new MsiStockHelper(
            $this->mockProductRepository,
            $this->mockSearchCriteriaBuilder,
            $this->mockSourceItemFactory,
            $this->mockSourceItemsSave,
            $this->mockStockResolver,
            $this->mockGetReservationsQuantity,
            $this->mockStockRepository,
            $this->mockSourcesAssignedToStock,
            $this->mockInventoryHelper,
        );
    }

    /**
     * @dataProvider testUpdateProductStockDataProvider
     * @param string $sku
     * @param int $inputQty
     * @param \DateTime $lastOrderExported
     * @param int $reservedQty
     * @param int $setQty
     * @param string $sourceCode
     */
    public function testUpdateProductStock(
        string $sku,
        float $inputQty,
        \DateTime $lastOrderExported,
        float $reservedQty,
        float $setQty,
        string $sourceCode,
        bool $negativeQtyEnabled
    ): void
    {
        $this->mockSearchCriteriaBuilder
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->mockSearchCriteria);

        $this->mockStockRepository
            ->expects($this->once())
            ->method('getList')
            ->with($this->mockSearchCriteria)
            ->willReturn($this->mockStockSearchResults);

        $this->mockStockSearchResults
            ->expects($this->once())
            ->method('getItems')
            ->willReturn([$this->mockStock]);

        $this->mockStock
            ->expects($this->exactly(2))
            ->method('getStockId')
            ->willReturn(1);

        $this->mockSourcesAssignedToStock
            ->expects($this->once())
            ->method('execute')
            ->with(1)
            ->willReturn([$this->mockSource]);

        $this->mockSource
            ->expects($this->once())
            ->method('getSourceCode')
            ->willReturn($sourceCode);

        $this->mockGetReservationsQuantity
            ->expects($this->once())
            ->method('execute')
            ->with($sku, 1)
            ->willReturn($reservedQty);

        $this->mockInventoryHelper
            ->expects($this->once())
            ->method('isNegativeQtyEnabled')
            ->willReturn($negativeQtyEnabled);

        $this->mockSourceItemFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->mockSourceItem);

        $this->mockSourceItem
            ->expects($this->once())
            ->method('setSourceCode')
            ->with($sourceCode)
            ->willReturnSelf();

        $this->mockSourceItem
            ->expects($this->once())
            ->method('setSku')
            ->with($sku)
            ->willReturnSelf();

        $this->mockSourceItem
            ->expects($this->once())
            ->method('setQuantity')
            ->with($setQty)
            ->willReturnSelf();

        $this->mockSourceItem
            ->expects($this->once())
            ->method('setStatus')
            ->with($setQty > 0 ? 1 : 0)
            ->willReturnSelf();

        $this->mockSourceItemsSave
            ->expects($this->once())
            ->method('execute')
            ->with([$this->mockSourceItem]);

        $this->stockHelper->updateProductStock(
            $sku,
            $inputQty,
            $lastOrderExported,
            $sourceCode
        );
    }

    public function testUpdateProductStockDataProvider()
    {
        /**
         * $sku
         * $inputQty
         * $lastOrderExported
         * $reservedQty
         * $setQty
         * $source
         * $negativeQtyEnabled
         */
        return [

            ['TEST-SKU', 10, new \DateTime(), 5, 5, 'warehouse', false],
            ['TEST-SKU', 10, new \DateTime(), 0, 10, 'default', false],
            ['TEST-SKU', 10, new \DateTime(), 11, 0, 'default', false],
            ['TEST-SKU', 10, new \DateTime(), 11, -1, 'default', true],
        ];
    }
}
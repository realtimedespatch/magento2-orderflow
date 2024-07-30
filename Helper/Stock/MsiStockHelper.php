<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Helper\Stock;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\InventoryApi\Api\GetSourcesAssignedToStockOrderedByPriorityInterface;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;
use Magento\InventoryApi\Api\StockRepositoryInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory;
use Magento\InventoryReservations\Model\ResourceModel\GetReservationsQuantity;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use RealtimeDespatch\OrderFlow\Helper\Import\Inventory;

class MsiStockHelper implements \RealtimeDespatch\OrderFlow\Api\StockHelperInterface
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $_productRepository;
    
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * @var \Magento\InventoryApi\Api\SourceItemsSaveInterface
     */
    protected SourceItemsSaveInterface $_sourceItemsSave;

    /**
     * @var \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory
     */
    protected SourceItemInterfaceFactory $_sourceItemFactory;

    /**
     * @var \Magento\InventorySalesApi\Api\StockResolverInterface
     */
    protected StockResolverInterface $_stockResolver;

    /**
     * @var Magento\InventoryReservationsApi\Model\ResourceModel\GetReservationsQuantity
     */
    protected GetReservationsQuantity $_stockReservations;

    /**
     * @var StockRepositoryInterface;
     */
    protected StockRepositoryInterface $_stockRepository;

    /**
     * @var GetSourcesAssignedToStockOrderedByPriorityInterface
     */
    protected GetSourcesAssignedToStockOrderedByPriorityInterface $_sourcesAssignedToStock;

    /**
     * @var \RealtimeDespatch\OrderFlow\Helper\Import\Inventory
     */
    protected $_helper;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SourceItemInterfaceFactory $sourceItemFactory,
        SourceItemsSaveInterface $sourceItemsSave,
        StockResolverInterface $stockResolver,
        GetReservationsQuantity $stockReservations,
        StockRepositoryInterface $stockRepository,
        GetSourcesAssignedToStockOrderedByPriorityInterface $sourcesAssignedToStock,
        Inventory $inventoryHelper
    )
    {
        $this->_productRepository = $productRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_sourceItemFactory = $sourceItemFactory;
        $this->_sourceItemsSave = $sourceItemsSave;
        $this->_stockResolver = $stockResolver;
        $this->_stockReservations = $stockReservations;
        $this->_stockRepository = $stockRepository;
        $this->_sourcesAssignedToStock = $sourcesAssignedToStock;
        $this->_helper = $inventoryHelper;
    }

    public function updateProductStock($sku, $qty, $lastOrderExported, $source = "default")
    {
        $inventory = $this->calculateProductStock($sku, $qty, $source);
        $qty = $inventory->unitsCalculated;
        if ( ! $this->_helper->isNegativeQtyEnabled() && $qty < 0) {
            $qty = 0;
        }
        $isInStock = $qty > 0 ? 1 : 0;

        $sourceItem = $this->_sourceItemFactory->create();
        $sourceItem->setSourceCode($source);
        $sourceItem->setSku($sku);
        $sourceItem->setQuantity($qty);
        $sourceItem->setStatus($isInStock);
        $this->_sourceItemsSave->execute([$sourceItem]);
    }

    /**
     * Calculates a product's stock taking into reservations
     *
     * @param string $productId Product SKU
     * @param integer $unitsReceived Units Received From OrderFlow
     *
     * @return \stdClass
     */
    public function calculateProductStock($sku, $unitsReceived, $source)
    {
        $inventory = new \stdClass;
        $inventory->unitsReceived = $unitsReceived;
        $inventory->unitsReserved = 0;

        // Check which stocks the source item belongs to
        foreach ($this->getAllStocks() as $stock) {
            $assignedSources = $this->_sourcesAssignedToStock->execute($stock->getStockId());
            // Check the assigned source codes for a match
            foreach ($assignedSources as $assignedSource) {
                if ($assignedSource->getSourceCode() == $source) {
                    // Add reservations for this stock to the total
                    $inventory->unitsReserved += $this->_stockReservations->execute($sku, $stock->getStockId());
                }
            }
        }

        // Adjust the unitsReceived to take into account reservations. OrderFlow will already have
        // deducted the reservations, so we need to add them back to the figure received.
        // NB reservations are negative, so using minus here to add back
        $inventory->unitsCalculated = $inventory->unitsReceived - $inventory->unitsReserved;

        return $inventory;
    }

    protected function getAllStocks()
    {
        // Get all stocks
        $searchCriteria = $this->_searchCriteriaBuilder->create();
        $stocks = $this->_stockRepository->getList($searchCriteria)->getItems();

        return $stocks;
    }
}

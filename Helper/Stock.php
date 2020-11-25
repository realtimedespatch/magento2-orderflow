<?php

namespace RealtimeDespatch\OrderFlow\Helper;

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use RealtimeDespatch\OrderFlow\Helper\Import\Inventory;
use stdClass;
use Zend_Db_Expr;

/**
 * Stock Helper.
 */
class Stock extends AbstractHelper
{
    const DEFAULT_SOURCE = 'default';

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var Inventory
     */
    protected $helper;

    /**
     * @param OrderCollectionFactory $orderFactory
     */
    protected $orderFactory;

    /**
     * @param QuoteCollectionFactory $quoteFactory
     */
    protected $quoteFactory;

    /**
     * @var SourceItemsSaveInterface
     */
    protected $sourceItemsSave;

    /**
     * @var SourceItemInterfaceFactory
     */
    protected $sourceItemFactory;

    /**
     * @param Context $context
     * @param ProductRepositoryInterface $productRepository
     * @param Inventory $inventoryHelper
     * @param OrderCollectionFactory $orderFactory
     * @param QuoteCollectionFactory $quoteFactory
     * @param SourceItemsSaveInterface $sourceItemSave
     * @param SourceItemInterfaceFactory $sourceItemFactory
     */
    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepository,
        Inventory $inventoryHelper,
        OrderCollectionFactory $orderFactory,
        QuoteCollectionFactory $quoteFactory,
        SourceItemsSaveInterface $sourceItemSave,
        SourceItemInterfaceFactory $sourceItemFactory
    ) {
        parent::__construct($context);

        $this->productRepository = $productRepository;
        $this->helper = $inventoryHelper;
        $this->orderFactory = $orderFactory;
        $this->quoteFactory = $quoteFactory;
        $this->sourceItemsSave = $sourceItemSave;
        $this->sourceItemFactory = $sourceItemFactory;
    }

    /**
     * Updates a product's stock.
     *
     * @param string $sku SKU
     * @param integer $qty Qty
     *
     * @return stdClass
     * @throws Exception
     */
    public function updateProductStock(string $sku, int $qty)
    {
        $product = $this->productRepository->get($sku);
        $inventory = $this->calculateProductStock($product->getId(), $qty);
        $isInStock = $inventory->unitsCalculated > 0 ? 1 : 0;

        if (! $this->helper->isNegativeQtyEnabled() && $inventory->unitsCalculated < 0) {
            $inventory->unitsCalculated = 0;
        }

        /** @var SourceItemInterface $sourceItem */
        /** @noinspection PhpUndefinedMethodInspection */
        $sourceItem = $this->sourceItemFactory->create();
        $sourceItem->setSourceCode(self::DEFAULT_SOURCE);
        $sourceItem->setSku($sku);
        $sourceItem->setQuantity($inventory->unitsCalculated);
        $sourceItem->setStatus($isInStock);
        $this->sourceItemsSave->execute([$sourceItem]);

        return $inventory;
    }

    /**
     * Calculates a product's stock taking into account unsent orders, and active quotes.
     *
     * @param integer $productId Product SKU
     * @param integer $unitsReceived Units Received From OrderFlow
     *
     * @return array|stdClass
     */
    public function calculateProductStock(int $productId, int $unitsReceived)
    {
        $inventory = new stdClass;

        $inventory->unitsReceived = $unitsReceived;
        $inventory->unitsUnsentOrders = $this->_calculateUnsentOrderUnits($productId);
        $inventory->unitsActiveQuotes = $this->_calculateActiveQuoteUnits($productId);

        $inventory->unitsCalculated = $inventory->unitsReceived;
        $inventory->unitsCalculated -= $inventory->unitsActiveQuotes;
        $inventory->unitsCalculated -= $inventory->unitsUnsentOrders;

        return $inventory;
    }

    /**
     * Calculates the number of units of a product that are yet to be integrated into OrderFlow for it's
     * inventory calculation.
     *
     * This is calculated by summing the number of units attached to non exported orders, and orders that have been
     * exported to OrderFlow after it's inventory calculation.
     *
     * @param integer $productId Product SKU
     * @return integer
     */
    protected function _calculateUnsentOrderUnits(int $productId)
    {
        if (! $this->helper->isUnsentOrderAdjustmentEnabled()) {
            return 0;
        }

        $cutoffDate = $this->helper->getUnsentOrderCutoffDate();
        $collection = $this->orderFactory->create();

        $collection
            ->getSelect()
            ->joinLeft(
                ['order_item' => $collection->getTable('sales_order_item')],
                'main_table.entity_id = order_item.order_id',
                ['qty' => new Zend_Db_Expr('sum(order_item.qty_ordered)')]
            )
            ->group('order_item.product_id');

        $collection
            ->addFieldToSelect('entity_id')
            ->addFieldToFilter('main_table.status', ['eq' => $this->helper->getValidUnsentOrderStatuses()])
            ->addFieldToFilter('order_item.product_id', ['eq' => $productId])
            ->addFieldToFilter('main_table.is_virtual', ['eq' => 0])
            ->addFieldToFilter('main_table.created_at', ['gteq' => $cutoffDate]);

        $data = $collection->getData();
        $unsentUnits = isset($data[0]['qty']) ? $data[0]['qty'] : 0;

        return max(0, $unsentUnits);
    }

    /**
     * Calculates the number of units for a product that are attached to active quotes - these are not taken into
     * account when performing the inventory calculation in OrderFlow.
     *
     * @param int $productId Product SKU
     *
     * @return integer
     */
    protected function _calculateActiveQuoteUnits(int $productId)
    {
        if (! $this->helper->isActiveQuoteAdjustmentEnabled()) {
            return 0;
        }

        $cutoffDate = $this->helper->getActiveQuoteCutoffDate();
        $collection = $this->quoteFactory->create();

        $collection
            ->getSelect()
            ->joinLeft(
                ['quote_item' => $collection->getTable('quote_item')],
                'main_table.entity_id = quote_item.quote_id',
                ['qty' => new Zend_Db_Expr('sum(quote_item.qty)')]
            )
            ->group('quote_item.product_id');

        $collection
            ->addFieldToSelect('entity_id')
            ->addFieldToFilter('main_table.is_active', ['eq' => 1])
            ->addFieldToFilter('quote_item.product_id', ['eq' => $productId])
            ->addFieldToFilter('main_table.updated_at', ['gteq' => $cutoffDate]);

        $data = $collection->getData();
        $activeUnits = isset($data[0]['qty']) ? $data[0]['qty'] : 0;

        return max(0, $activeUnits);
    }
}

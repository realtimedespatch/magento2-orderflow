<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Helper\Stock;

use Magento\Catalog\Api\ProductRepositoryInterface;
use RealtimeDespatch\OrderFlow\Helper\Import\Inventory;
use Magento\Sales\Model\OrderFactory;
use Magento\Quote\Model\QuoteFactory;

class LegacyStockHelper implements \RealtimeDespatch\OrderFlow\Api\StockHelperInterface
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $_productRepository;

    /**
     * @var RealtimeDespatch\OrderFlow\Helper\Import\Inventory
     */
    protected $_helper;

    /**
     * @param OrderFactory $orderFactory
     */
    protected $_orderFactory;

    /**
     * @param QuoteFactory $quoteFactory
     */
    protected $_quoteFactory;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param Inventory $inventoryHelper
     * @param OrderFactory $orderFactory
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        Inventory $inventoryHelper,
        OrderFactory $orderFactory,
        QuoteFactory $quoteFactory
    )
    {
        $this->_productRepository = $productRepository;
        $this->_helper = $inventoryHelper;
        $this->_orderFactory = $orderFactory;
        $this->_quoteFactory = $quoteFactory;
    }

    public function updateProductStock($sku, $qty, $lastOrderExported, $source = "default")
    {
        $product = $this->_productRepository->get($sku);
        $inventory = $this->calculateProductStock($product->getId(), $qty, $lastOrderExported);
        $qty = $inventory->unitsCalculated;
        if (!$this->_helper->isNegativeQtyEnabled() && $qty < 0) {
            $qty = 0;
        }
        $isInStock = $qty > 0 ? 1 : 0;

        $product->setStockData(['qty' => $qty, 'is_in_stock' => $isInStock]);
        $product->setQuantityAndStockStatus(['qty' => $qty, 'is_in_stock' => $isInStock]);
        $this->_productRepository->save($product);
    }

    /**
     * Calculates a product's stock taking into account unsent orders, and active quotes.
     *
     * @param integer $productId Product SKU
     * @param integer $unitsReceived Units Received From OrderFlow
     * @param \DateTime $lastOrderExported Last Order Exported Date
     *
     * @return array
     */
    public function calculateProductStock($productId, $unitsReceived, $lastOrderExported)
    {
        $inventory = new \stdClass;

        $inventory->unitsReceived = $unitsReceived;
        $inventory->unitsUnsentOrders = $this->_calculateUnsentOrderUnits($productId, $lastOrderExported);
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
     * This is calculated by summing the number of units attached to unexported orders, and orders that have been
     * exported to OrderFlow after it's inventory calculation.
     *
     * @param integer $productId Product SKU
     * @param integer $unitsReceived Units Received From OrderFlow
     *
     * @return integer
     */
    protected function _calculateUnsentOrderUnits($productId, $lastOrderExported)
    {
        if (!$this->_helper->isUnsentOrderAdjustmentEnabled()) {
            return 0;
        }

        $cutoffDate = $this->_helper->getUnsentOrderCutoffDate();
        $collection = $this->_orderFactory->create()->getCollection();

        $collection
            ->getSelect()
            ->joinLeft(
                ['order_item' => $collection->getTable('sales_order_item')],
                'main_table.entity_id = order_item.order_id',
                ['qty' => new \Zend_Db_Expr('sum(order_item.qty_ordered)')]
            )
            ->group('order_item.product_id');

        $collection
            ->addFieldToSelect('entity_id')
            ->addFieldToFilter('main_table.status', ['eq' => $this->_helper->getValidUnsentOrderStatuses()])
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
     * @param integer $productId Product SKU
     *
     * @return integer
     */
    protected function _calculateActiveQuoteUnits($productId)
    {
        if (!$this->_helper->isActiveQuoteAdjustmentEnabled()) {
            return 0;
        }

        $cutoffDate = $this->_helper->getActiveQuoteCutoffDate();
        $collection = $this->_quoteFactory->create()->getCollection();

        $collection
            ->getSelect()
            ->joinLeft(
                ['quote_item' => $collection->getTable('quote_item')],
                'main_table.entity_id = quote_item.quote_id',
                ['qty' => new \Zend_Db_Expr('sum(quote_item.qty)')]
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
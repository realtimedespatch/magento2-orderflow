<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Helper\Stock;

use Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;

class MsiStockHelper extends AbstractStockHelper
{
    protected SourceItemsSaveInterface $_sourceItemsSave;
    protected SourceItemInterfaceFactory $_sourceItemFactory;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \RealtimeDespatch\OrderFlow\Helper\Import\Inventory $inventoryHelper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        SourceItemInterfaceFactory $sourceItemFactory,
        SourceItemsSaveInterface $sourceItemsSave
    )
    {
        $this->_sourceItemsSave = $sourceItemsSave;
        $this->_sourceItemFactory = $sourceItemFactory;
        parent::__construct(
            $context, $productRepository, $inventoryHelper,
            $orderFactory, $quoteFactory, $moduleManager
        );
    }

    public function updateProductStock($sku, $qty, $lastOrderExported, $source = "default")
    {
        $product = $this->_productRepository->get($sku);

        if (!$this->_helper->isNegativeQtyEnabled() && $qty < 0) {
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
}
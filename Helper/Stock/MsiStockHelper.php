<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Helper\Stock;

use Magento\Catalog\Api\ProductRepositoryInterface;
use RealtimeDespatch\OrderFlow\Helper\Import\Inventory;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory;

class MsiStockHelper implements \RealtimeDespatch\OrderFlow\Api\StockHelperInterface
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $_productRepository;

    /**
     * @var \RealtimeDespatch\OrderFlow\Helper\Import\Inventory
     */
    protected $_helper;

    /**
     * @var \Magento\InventoryApi\Api\SourceItemsSaveInterface
     */
    protected SourceItemsSaveInterface $_sourceItemsSave;

    /**
     * @var \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory;
     */
    protected SourceItemInterfaceFactory $_sourceItemFactory;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        Inventory $inventoryHelper,
        SourceItemInterfaceFactory $sourceItemFactory,
        SourceItemsSaveInterface $sourceItemsSave
    )
    {
        $this->_productRepository = $productRepository;
        $this->_helper = $inventoryHelper;
        $this->_sourceItemsSave = $sourceItemsSave;
        $this->_sourceItemFactory = $sourceItemFactory;
    }

    public function updateProductStock($sku, $qty, $lastOrderExported, $source = "default")
    {
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

}

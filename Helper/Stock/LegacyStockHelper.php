<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Helper\Stock;

use Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;

class LegacyStockHelper extends AbstractStockHelper
{
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
}
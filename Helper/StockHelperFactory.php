<?php

namespace RealtimeDespatch\OrderFlow\Helper;

use Magento\Framework\Module\Manager;
use RealtimeDespatch\OrderFlow\Api\StockHelperInterface;
use RealtimeDespatch\OrderFlow\Helper\Stock\MsiStockHelperFactory;
use RealtimeDespatch\OrderFlow\Helper\Stock\LegacyStockHelperFactory;

/**
 * Stock Helper Factory
 */
class StockHelperFactory
{
    public function __construct(
        protected MsiStockHelperFactory $msiStockHelperFactory,
        protected LegacyStockHelperFactory $legacyStockHelperFactory,
        protected Manager $moduleManager
    )
    {
    }

    public function create(): StockHelperInterface
    {
        if ($this->moduleManager->isEnabled('Magento_InventoryApi')) {
            return $this->msiStockHelperFactory->create();
        } else {
            return $this->legacyStockHelperFactory->create();
        }
    }
}

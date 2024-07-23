<?php

namespace RealtimeDespatch\OrderFlow\Helper;

use Magento\Framework\Module\Manager;
use RealtimeDespatch\OrderFlow\Api\StockHelperInterface;
use RealtimeDespatch\OrderFlow\Helper\Stock\MsiStockHelperFactory;
use RealtimeDespatch\OrderFlow\Helper\Stock\LegacyStockHelperFactory;

/**
 * Stock Helper.
 */
class StockHelperFactory extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function __construct(
        protected \Magento\Framework\App\Helper\Context $context,
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

<?php

namespace RealtimeDespatch\OrderFlow\Helper;

use RealtimeDespatch\OrderFlow\Helper\Stock\MsiStockHelperFactory;
use RealtimeDespatch\OrderFlow\Helper\Stock\LegacyStockHelperFactory;
use Magento\Framework\Module\Manager;
use RealtimeDespatch\OrderFlow\Api\StockHelperInterface;

/**
 * Stock Helper Factory
 */
class StockHelperFactory
{
    /**
     * @var MsiStockHelperFactory;
     */
    protected $_msiStockHelperFactory;

    /**
     * @var LegacyStockHelperFactory
     */
    protected $_legacyStockHelperFactory;

    /**
     * @var Manager
     */
    protected $_moduleManager;

    public function __construct(
        protected MsiStockHelperFactory $msiStockHelperFactory,
        protected LegacyStockHelperFactory $legacyStockHelperFactory,
        protected Manager $moduleManager
    )
    {
        $this->_msiStockHelperFactory = $msiStockHelperFactory;
        $this->_legacyStockHelperFactory = $legacyStockHelperFactory;
        $this->_moduleManager = $moduleManager;
    }

    public function create(): StockHelperInterface
    {
        if ($this->_moduleManager->isEnabled('Magento_InventoryApi')) {
            return $this->_msiStockHelperFactory->create();
        } else {
            return $this->_legacyStockHelperFactory->create();
        }
    }
}

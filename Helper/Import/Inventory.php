<?php

namespace RealtimeDespatch\OrderFlow\Helper\Import;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use RealtimeDespatch\OrderFlow\Api\ImportHelperInterface;

/**
 * Inventory Import Helper.
 */
class Inventory extends AbstractHelper implements ImportHelperInterface
{
    /**
     * Checks whether the import process is enabled.
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            'orderflow_inventory_import/settings/is_enabled',
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Returns the maximum batch size for processing.
     *
     * @return integer
     */
    public function getBatchSize()
    {
        return (integer) $this->scopeConfig->getValue(
            'orderflow_inventory_import/settings/batch_size',
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Checks whether negative stock quantities are enabled.
     *
     * @return boolean
     */
    public function isNegativeQtyEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            'orderflow_inventory_import/settings/negative_qtys_enabled',
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Checks whether stock adjustments are to be calculated, and applied.
     *
     * @return boolean
     */
    public function isInventoryAdjustmentEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            'orderflow_inventory_import/settings/adjust_inventory',
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Checks whether adjustments are to be calculated for unsent orders.
     *
     * @return boolean
     */
    public function isUnsentOrderAdjustmentEnabled()
    {
        $flag = (integer) $this->scopeConfig->getValue(
            'orderflow_inventory_import/settings/adjust_inventory',
            ScopeInterface::SCOPE_WEBSITE
        );

        return $flag > 0;
    }

    /**
     * Checks whether adjustments are to be calculated for active quotes.
     *
     * @return boolean
     */
    public function isActiveQuoteAdjustmentEnabled()
    {
        $flag = (integer) $this->scopeConfig->getValue(
            'orderflow_inventory_import/settings/adjust_inventory',
            ScopeInterface::SCOPE_WEBSITE
        );

        return $flag > 1;
    }

    /**
     * Returns the valid unsent order statuses.
     *
     * @return array
     */
    public function getValidUnsentOrderStatuses()
    {
        $statuses = $this->scopeConfig->getValue(
            'orderflow_inventory_import/settings/valid_unsent_order_statuses',
            ScopeInterface::SCOPE_WEBSITE
        );

        return explode(',', $statuses);
    }

    /**
     * Retrieves the active quote cutoff in days.
     *
     * @return integer
     */
    public function getActiveQuoteCutoff()
    {
        return (integer) $this->scopeConfig->getValue(
            'orderflow_inventory_import/settings/active_quote_cutoff',
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Retrieves the active quote cutoff date.
     *
     * @return string
     */
    public function getActiveQuoteCutoffDate()
    {
        return date('Y-m-d H:i:s', strtotime('-'.$this->getActiveQuoteCutoff().' days'));
    }

    /**
     * Retrieves the unsent order cutoff in days.
     *
     * @return integer
     */
    public function getUnsentOrderCutoff()
    {
        return (integer) $this->scopeConfig->getValue(
            'orderflow_inventory_import/settings/unsent_order_cutoff',
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Retrieves the unsent order cutoff date.
     *
     * @return string
     */
    public function getUnsentOrderCutoffDate()
    {
        return date('Y-m-d H:i:s', strtotime('-'.$this->getUnsentOrderCutoff().' days'));
    }
}

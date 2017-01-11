<?php

namespace RealtimeDespatch\OrderFlow\Helper\Import;

/**
 * Shipment Import Helper.
 */
class Shipment extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Checks whether the import process is enabled.
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            'orderflow_shipment_import/settings/is_enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Returns the maximum batch size for processing.
     *
     * @return boolean
     */
    public function getBatchSize()
    {
        return (integer) $this->scopeConfig->getValue(
            'orderflow_shipment_import/settings/batch_size',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }
}
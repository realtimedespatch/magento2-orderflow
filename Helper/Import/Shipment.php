<?php

namespace RealtimeDespatch\OrderFlow\Helper\Import;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use RealtimeDespatch\OrderFlow\Api\ImportHelperInterface;

/**
 * Shipment Import Helper.
 */
class Shipment extends AbstractHelper implements ImportHelperInterface
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
            'orderflow_shipment_import/settings/batch_size',
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}

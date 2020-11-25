<?php

namespace RealtimeDespatch\OrderFlow\Helper\Log;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Log Cleaning Helper.
 */
class Cleaning extends AbstractHelper
{
    /**
     * Checks whether the log cleaning process is enabled.
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            'orderflow_log_cleaning/settings/is_enabled',
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Returns the retention duration for export logs in days.
     *
     * @return integer
     */
    public function getExportLogDuration()
    {
        return (integer) $this->scopeConfig->getValue(
            'orderflow_log_cleaning/settings/export_duration',
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Returns the retention duration for import logs in days.
     *
     * @return integer
     */
    public function getImportLogDuration()
    {
        return (integer) $this->scopeConfig->getValue(
            'orderflow_log_cleaning/settings/import_duration',
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Returns the retention duration for request logs in days.
     *
     * @return integer
     */
    public function getRequestLogDuration()
    {
        return (integer) $this->scopeConfig->getValue(
            'orderflow_log_cleaning/settings/request_duration',
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}

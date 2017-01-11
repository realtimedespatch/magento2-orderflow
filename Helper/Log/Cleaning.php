<?php

namespace RealtimeDespatch\OrderFlow\Helper\Log;

/**
 * Log Cleaning Helper.
 */
class Cleaning extends \Magento\Framework\App\Helper\AbstractHelper
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
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Returns the retention duration for export logs.
     *
     * @return boolean
     */
    public function getExportLogDuration()
    {
        return (integer) $this->scopeConfig->getValue(
            'orderflow_log_cleaning/settings/export_duration',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Returns the retention duration for import logs.
     *
     * @return boolean
     */
    public function getImportLogDuration()
    {
        return (integer) $this->scopeConfig->getValue(
            'orderflow_log_cleaning/settings/import_duration',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Returns the retention duration for request logs.
     *
     * @return boolean
     */
    public function getRequestLogDuration()
    {
        return (integer) $this->scopeConfig->getValue(
            'orderflow_log_cleaning/settings/request_duration',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }
}
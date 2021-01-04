<?php

namespace RealtimeDespatch\OrderFlow\Helper\Log;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\ScopeInterface;

/**
 * Log Cleaning Helper.
 */
class Cleaning extends AbstractHelper
{
    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @param Context $context
     * @param DateTime $dateTime
     */
    public function __construct(
        Context $context,
        DateTime $dateTime
    ) {
        parent::__construct($context);

        $this->dateTime = $dateTime;
    }

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
        $duration = (integer) $this->scopeConfig->getValue(
            'orderflow_log_cleaning/settings/export_duration',
            ScopeInterface::SCOPE_WEBSITE
        );

        return max($duration, 1);
    }

    /**
     * Export Log Expiration Date Getter.
     *
     * @return integer
     */
    public function getExportLogExpirationDate()
    {
        return $this->dateTime->date(
            'Y-m-d',
            '-'.($this->getExportLogDuration() - 1).' days'
        );
    }

    /**
     * Returns the retention duration for import logs in days.
     *
     * @return integer
     */
    public function getImportLogDuration()
    {
        $duration =  (integer) $this->scopeConfig->getValue(
            'orderflow_log_cleaning/settings/import_duration',
            ScopeInterface::SCOPE_WEBSITE
        );

        return max($duration, 1);
    }

    /**
     * Import Log Expiration Date Getter.
     *
     * @return integer
     */
    public function getImportLogExpirationDate()
    {
        return $this->dateTime->date(
            'Y-m-d',
            '-'.($this->getImportLogDuration() - 1).' days'
        );
    }

    /**
     * Returns the retention duration for request logs in days.
     *
     * @return integer
     */
    public function getRequestLogDuration()
    {
        $duration =  (integer) $this->scopeConfig->getValue(
            'orderflow_log_cleaning/settings/request_duration',
            ScopeInterface::SCOPE_WEBSITE
        );

        return max($duration, 1);
    }

    /**
     * Request Log Expiration Date Getter.
     *
     * @return integer
     */
    public function getRequestLogExpirationDate()
    {
        return $this->dateTime->date(
            'Y-m-d',
            '-'.($this->getRequestLogDuration() - 1).' days'
        );
    }
}

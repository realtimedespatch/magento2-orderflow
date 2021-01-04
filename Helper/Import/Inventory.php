<?php

namespace RealtimeDespatch\OrderFlow\Helper\Import;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\ScopeInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ImportInterface;
use RealtimeDespatch\OrderFlow\Api\ImportHelperInterface;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request\Collection;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request\CollectionFactory as RequestCollectionFactory;

/**
 * Inventory Import Helper.
 */
class Inventory extends AbstractHelper implements ImportHelperInterface
{
    /**
     * @var DateTime
     */
    protected $datetime;

    /**
     * @var RequestCollectionFactory
     */
    protected $reqCollectionFactory;

    /**
     * @param Context $context
     * @param DateTime $dateTime
     * @param RequestCollectionFactory $reqCollectionFactory
     */
    public function __construct(
        Context $context,
        DateTime $dateTime,
        RequestCollectionFactory $reqCollectionFactory
    ) {
        parent::__construct($context);

        $this->datetime = $dateTime;
        $this->reqCollectionFactory = $reqCollectionFactory;
    }

    /**
     * Checks whether the import process is enabled.
     *
     * @return boolean
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            'orderflow_inventory_import/settings/is_enabled',
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Importable Requests Getter.
     *
     * @return array
     */
    public function getImportableRequests(): array
    {
        /** @var Collection $collection */
        $collection = $this->reqCollectionFactory->create();

        return $collection->getImportableRequests(
            ImportInterface::ENTITY_INVENTORY,
            $this->getBatchSize()
        );
    }

    /**
     * Returns the maximum batch size for processing.
     *
     * @return integer
     */
    public function getBatchSize(): int
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
    public function isNegativeQtyEnabled(): bool
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
    public function isInventoryAdjustmentEnabled(): bool
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
    public function isUnsentOrderAdjustmentEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            'orderflow_inventory_import/settings/adjust_inventory',
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Checks whether adjustments are to be calculated for active quotes.
     *
     * @return boolean
     */
    public function isActiveQuoteAdjustmentEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            'orderflow_inventory_import/settings/adjust_inventory',
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Returns the valid unsent order statuses.
     *
     * @return array
     */
    public function getValidUnsentOrderStatuses(): array
    {
        $statuses = $this->scopeConfig->getValue(
            'orderflow_inventory_import/settings/valid_unsent_order_statuses',
            ScopeInterface::SCOPE_WEBSITE
        );

        if (! $statuses) {
            return [];
        }

        return explode(',', $statuses);
    }

    /**
     * Retrieves the active quote cutoff in days.
     *
     * @return integer
     */
    public function getActiveQuoteCutoff(): int
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
    public function getActiveQuoteCutoffDate(): string
    {
        return $this->datetime->date(
            'Y-m-d H:i:s',
            '-'.$this->getActiveQuoteCutoff().' days'
        );
    }

    /**
     * Retrieves the unsent order cutoff in days.
     *
     * @return integer
     */
    public function getUnsentOrderCutoff(): int
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
    public function getUnsentOrderCutoffDate(): string
    {
        return $this->datetime->date(
            'Y-m-d H:i:s',
            '-'.$this->getUnsentOrderCutoff().' days'
        );
    }
}

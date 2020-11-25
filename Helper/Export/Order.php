<?php

namespace RealtimeDespatch\OrderFlow\Helper\Export;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Website;
use RealtimeDespatch\OrderFlow\Api\ExportHelperInterface;

/**
 * Order Export Helper.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Order extends AbstractHelper implements ExportHelperInterface
{
    const STATUS_PENDING = 'Pending';
    const STATUS_QUEUED = 'Queued';

    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @param Context $context
     * @param OrderCollectionFactory $orderCollectionFactory
     */
    public function __construct(
        Context $context,
        OrderCollectionFactory $orderCollectionFactory
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Checks whether the export process is enabled.
     *
     * @param integer|null $scopeId
     *
     * @return boolean
     */
    public function isEnabled($scopeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            'orderflow_order_export/settings/is_enabled',
            ScopeInterface::SCOPE_WEBSITE,
            $scopeId
        );
    }

    /**
     * Returns the maximum batch size for processing.
     *
     * @param integer|null $scopeId
     *
     * @return int
     */
    public function getBatchSize($scopeId = null)
    {
        return (integer) $this->scopeConfig->getValue(
            'orderflow_order_export/settings/batch_size',
            ScopeInterface::SCOPE_WEBSITE,
            $scopeId
        );
    }

    /**
     * Returns the exportable order statuses.
     *
     * @param integer|null $scopeId
     *
     * @return array
     */
    public function getExportableOrderStatuses($scopeId = null)
    {
        $statuses = $this->scopeConfig->getValue(
            'orderflow_order_export/settings/exportable_status',
            ScopeInterface::SCOPE_WEBSITE,
            $scopeId
        );

        return explode(',', $statuses);
    }

    /**
     * Checks whether an order can be exported.
     *
     * @param string $orderStatus
     * @param string $exportStatus
     * @param integer|null $scopeId
     *
     * @return boolean
     */
    public function canExport(string $orderStatus, string $exportStatus, $scopeId = null)
    {
        // If the order is not in an exportable order status return false;
        if (! in_array($orderStatus, $this->getExportableOrderStatuses($scopeId))) {
            return false;
        }

        return ( ! $exportStatus) || $exportStatus == self::STATUS_PENDING;
    }

    /**
     * Returns a collection of createable orders.
     *
     * @param Website $website
     *
     * @return Collection
     */
    public function getCreateableOrders(Website $website)
    {
        return $this
            ->orderCollectionFactory
            ->create()
            ->addFieldToFilter('store_id', ['in' => $website->getStoreIds()])
            ->addFieldToFilter('status', ['in' => $this->getExportableOrderStatuses($website->getId())])
            ->addFieldToFilter('is_virtual', ['eq' => 0])
            ->addFieldToFilter('orderflow_export_date', ['null' => true])
            ->addFieldToFilter('orderflow_export_status', [
                ['neq' => self::STATUS_QUEUED],
                ['null' => true],
            ])
            ->setPage(1, $this->getBatchSize($website->getId()));
    }
}

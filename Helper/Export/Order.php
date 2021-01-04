<?php

namespace RealtimeDespatch\OrderFlow\Helper\Export;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Website;
use RealtimeDespatch\OrderFlow\Api\Data\ExportInterface;
use RealtimeDespatch\OrderFlow\Api\ExportHelperInterface;
use RealtimeDespatch\OrderFlow\Model\Source\Export\Status as ExportStatus;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request\Collection as RequestCollection;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request\CollectionFactory as RequestCollectionFactory;

/**
 * Order Export Helper.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Order extends AbstractHelper implements ExportHelperInterface
{
    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var RequestCollectionFactory
     */
    protected $reqCollectionFactory;

    /**
     * @param Context $context
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param RequestCollectionFactory $reqCollectionFactory
     */
    public function __construct(
        Context $context,
        OrderCollectionFactory $orderCollectionFactory,
        RequestCollectionFactory $reqCollectionFactory
    ) {
        parent::__construct($context);

        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->reqCollectionFactory = $reqCollectionFactory;
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

        if (! $statuses) {
            return [];
        }

        return explode(',', $statuses);
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
                ['neq' => ExportStatus::STATUS_QUEUED],
                ['null' => true],
            ])
            ->setPage(1, $this->getBatchSize($website->getId()));
    }

    /**
     * Exportable Requests Getter.
     *
     * @param integer|null $scopeId
     *
     * @return RequestCollection
     */
    public function getExportableRequests($scopeId = null)
    {
        /** @var RequestCollection $collection */
        $collection = $this->reqCollectionFactory->create();

        return $collection->getExportableRequests(
            ExportInterface::ENTITY_ORDER,
            $scopeId,
            $this->getBatchSize()
        );
    }
}

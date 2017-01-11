<?php

namespace RealtimeDespatch\OrderFlow\Helper\Export;

/**
 * Order Export Helper.
 */
class Order extends \Magento\Framework\App\Helper\AbstractHelper
{
    const STATUS_PENDING = 'Pending';

    /**
     * @var \Mage\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Mage\Sales\Model\OrderFactory $orderFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Sales\Model\OrderFactory $orderFactory
    )
    {
        $this->_orderFactory = $orderFactory;
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
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $scopeId
        );
    }

    /**
     * Returns the maximum batch size for processing.
     *
     * @param integer|null $scopeId
     *
     * @return boolean
     */
    public function getBatchSize($scopeId = null)
    {
        return (integer) $this->scopeConfig->getValue(
            'orderflow_order_export/settings/batch_size',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
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
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
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
    public function canExport($orderStatus, $exportStatus, $scopeId = null)
    {
        // If the order is not in an exportable order status return false;
        if ( ! in_array($orderStatus, $this->getExportableOrderStatuses($scopeId))) {
            return false;
        }

        return ( ! $exportStatus) || $exportStatus == self::STATUS_PENDING;
    }

    /**
     * Returns a collection of createable orders.
     *
     * @param \Magento\Store\Model\Website $website
     *
     * @return array
     */
    public function getCreateableOrders($website)
    {
        return $this->_orderFactory
            ->create()
            ->getCollection()
            ->addFieldToFilter('store_id', ['in' => $website->getStoreIds()])
            ->addFieldToFilter('status', ['in' => $this->getExportableOrderStatuses($website->getId())])
            ->addFieldToFilter('is_virtual', ['eq' => 0])
            ->addFieldToFilter('orderflow_export_date', ['null' => true])
            ->addFieldToFilter('orderflow_export_status', ['neq' => 'Queued'])
            ->setPage(1, $this->getBatchSize($website->getId()));
    }
}
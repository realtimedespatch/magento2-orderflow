<?php

namespace RealtimeDespatch\OrderFlow\Helper\Export;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Order Export Helper.
 */
class Order extends \Magento\Framework\App\Helper\AbstractHelper
{
    const STATUS_PENDING = 'Pending';
    const STATUS_QUEUED = 'Queued';

    /**
     * @var \Mage\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var TimezoneInterface
     */
    protected TimezoneInterface $_timezone;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Mage\Sales\Model\OrderFactory $orderFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        TimezoneInterface $timezone
    )
    {
        $this->_orderFactory = $orderFactory;
        $this->_timezone = $timezone;

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
     * Returns the minimum datetime from which orders should be exported if set in config
     *
     * @param $scopeId
     * @return false|string
     * @throws \Exception
     */
    public function getMinOrderDatetime($scopeId = null)
    {
        $value = $this->scopeConfig->getValue(
            'orderflow_order_export/settings/min_order_datetime',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $scopeId
        );
        if (empty($value)) {
            return false;
        }
        // datetime will be stored in admin timezone, convert to UTC
        $adminTimezone = $this->_timezone->getConfigTimezone(
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
        $datetime = new \DateTime($value, new \DateTimeZone($adminTimezone));
        $datetime->setTimezone(new \DateTimeZone('UTC'));

        return $datetime->format('Y-m-d H:i:s');
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
        $orders = $this->_orderFactory
            ->create()
            ->getCollection()
            ->addFieldToFilter('store_id', ['in' => $website->getStoreIds()])
            ->addFieldToFilter('status', ['in' => $this->getExportableOrderStatuses($website->getId())])
            ->addFieldToFilter('is_virtual', ['eq' => 0])
            ->addFieldToFilter('orderflow_export_date', ['null' => true])
            ->addFieldToFilter('orderflow_export_status', [
                ['neq' => self::STATUS_QUEUED],
                ['null' => true],
            ])
            ->setPage(1, $this->getBatchSize($website->getId()));

        if ($datetime = $this->getMinOrderDatetime($website->getId())) {
            $orders->addFieldToFilter('created_at', ['gt' => $datetime]);
        }

        return $orders;
    }
}

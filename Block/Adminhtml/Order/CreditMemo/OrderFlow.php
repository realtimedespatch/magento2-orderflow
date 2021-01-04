<?php

/** @noinspection PhpDeprecationInspection */

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Order\CreditMemo;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Sales\Block\Adminhtml\Order\AbstractOrder;
use Magento\Sales\Helper\Admin;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;
use RealtimeDespatch\OrderFlow\Helper\Admin\Info;
use RealtimeDespatch\OrderFlow\Helper\Api;

class OrderFlow extends AbstractOrder
{
    /**
     * @var Info
     */
    protected $adminInfoHelper;

    /**
     * @var Api
     */
    protected $apiHelper;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Admin $adminHelper
     * @param Info $adminInfoHelper
     * @param Api $apiHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Admin $adminHelper,
        Info $adminInfoHelper,
        Api $apiHelper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $adminHelper, $data);

        $this->adminInfoHelper = $adminInfoHelper;
        $this->apiHelper = $apiHelper;
    }

    /**
     * Retrieve invoice order
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->getCreditmemo()->getOrder();
    }

    /**
     * Retrieve creditmemo model instance
     *
     * @return Creditmemo
     */
    public function getCreditmemo()
    {
        return $this->_coreRegistry->registry('current_creditmemo');
    }

    /**
     * Checks whether the admin info block is enabled.
     *
     * @return bool
     */
    public function canDisplayAdminInfo()
    {
        return $this->adminInfoHelper->isEnabled();
    }

    /**
     * Returns the OrderFlow URL for the order.
     *
     * @return string
     */
    public function getOrderFlowOrderUrl()
    {
        $order = $this->getOrder();
        $storeId = $order->getStoreId();

        $url  = $this->apiHelper->getEndpoint($storeId);
        $url .= 'despatch/order/referenceDetail.htm?externalReference=';
        $url .= urlencode($order->getIncrementId());
        $url .= '&channel='.urlencode($this->apiHelper->getChannel($storeId));

        return $url;
    }
}

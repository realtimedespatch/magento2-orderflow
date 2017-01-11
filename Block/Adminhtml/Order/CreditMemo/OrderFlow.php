<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Order\CreditMemo;

class OrderFlow extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder
{
    protected $_adminInfoHelper;
    protected $_apiHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param \RealtimeDespatch\OrderFlow\Helper\Admin\Info $adminInfoHelper
     * @param \RealtimeDespatch\OrderFlow\Helper\Api $apiHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \RealtimeDespatch\OrderFlow\Helper\Admin\Info $adminInfoHelper,
        \RealtimeDespatch\OrderFlow\Helper\Api $apiHelper,
        array $data = []
    ) {
        $this->_adminInfoHelper = $adminInfoHelper;
        $this->_apiHelper = $apiHelper;
        parent::__construct($context, $registry, $adminHelper);
    }

    /**
     * Retrieve invoice order
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getCreditmemo()->getOrder();
    }

    /**
     * Retrieve creditmemo model instance
     *
     * @return \Magento\Sales\Model\Order\Creditmemo
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
        return $this->_adminInfoHelper->isEnabled();
    }

    /**
     * Returns the OrderFlow URL for the order.
     *
     * @return string
     */
    public function getOrderFlowOrderUrl()
    {
        $order = $this->getOrder();
        $storeId = $this->getStoreId();

        $url  = $this->_apiHelper->getEndpoint($storeId);
        $url .= 'despatch/order/referenceDetail.htm?externalReference=';
        $url .= urlencode($order->getIncrementId());
        $url .= '&channel='.urlencode($this->_apiHelper->getChannel($storeId));

        return $url;
    }
}
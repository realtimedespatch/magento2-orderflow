<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Order\View\Tab;

class OrderFlow extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
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
     * Checks whether the admin info block is enabled.
     *
     * @return bool
     */
    public function canDisplayAdminInfo()
    {
        return $this->_adminInfoHelper->isEnabled();
    }

    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
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

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('OrderFlow');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('OrderFlow Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
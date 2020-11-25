<?php

/** @noinspection PhpDeprecationInspection */

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Order\View\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Registry;
use Magento\Sales\Block\Adminhtml\Order\AbstractOrder;
use Magento\Sales\Helper\Admin;
use Magento\Sales\Model\Order;
use RealtimeDespatch\OrderFlow\Helper\Admin\Info;
use RealtimeDespatch\OrderFlow\Helper\Api;

class OrderFlow extends AbstractOrder implements TabInterface
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
        $this->adminInfoHelper = $adminInfoHelper;
        $this->apiHelper = $apiHelper;
        parent::__construct($context, $registry, $adminHelper, $data);
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
     * Retrieve order model instance
     *
     * @return Order
     */
    public function getOrder()
    {
        /** @noinspection PhpDeprecatedMethodInspection */
        return $this->_coreRegistry->registry('current_order');
    }

    /**
     * Returns the OrderFlow URL for the order.
     *
     * @return string
     */
    public function getOrderFlowOrderUrl()
    {
        $storeId = $this->getOrder()->getStoreId();

        $url  = $this->apiHelper->getEndpoint($storeId);
        $url .= 'despatch/order/referenceDetail.htm?externalReference=';
        $url .= urlencode($this->getOrder()->getIncrementId());
        $url .= '&channel='.urlencode($this->apiHelper->getChannel($storeId));

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

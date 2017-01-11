<?php

namespace RealtimeDespatch\OrderFlow\Plugin\Adminhtml;

/**
 * Class OrderView
 * @package RealtimeDespatch\OrderFlow\Plugin\Adminhtml
 */
class OrderView
{
    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_auth;

    /**
     * OrderView constructor.
     * @param \Magento\Framework\AuthorizationInterface $auth
     */
    public function __construct(\Magento\Framework\AuthorizationInterface $auth)
    {
        $this->_auth = $auth;
    }

    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\View $view
     */
    public function beforeSetLayout(\Magento\Sales\Block\Adminhtml\Order\View $view)
    {
        if ( ! $this->_auth->isAllowed('RealtimeDespatch_OrderFlow::orderflow_exports_orders')) {
            return;
        }

        $exportUrl = $view->getUrl('orderflow/order/export');
        $message = __('Are you sure you wish to export this order?');

        $view->addButton(
            'order_export',
            [
                'label' => __('Export'),
                'class' => 'export',
                'id' => 'order-view-export-button',
                'onclick' => "confirmSetLocation('{$message}', '{$exportUrl}')"
            ]
        );
    }
}
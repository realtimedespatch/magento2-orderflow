<?php

namespace RealtimeDespatch\OrderFlow\Plugin\Adminhtml;

use Magento\Framework\AuthorizationInterface;
use Magento\Sales\Block\Adminhtml\Order\View;

/**
 * Adds an Export Button to Order View.
 */
class OrderView
{
    /**
     * @var AuthorizationInterface
     */
    protected $_auth;

    /**
     * OrderView constructor.
     * @param AuthorizationInterface $auth
     */
    public function __construct(AuthorizationInterface $auth)
    {
        $this->_auth = $auth;
    }

    /**
     * @param View $view
     */
    public function beforeSetLayout(View $view)
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
                'id' => 'orderflow-order-view-export-button',
                'onclick' => "confirmSetLocation('{$message}', '{$exportUrl}')"
            ]
        );
    }
}

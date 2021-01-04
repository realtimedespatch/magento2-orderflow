<?php

namespace RealtimeDespatch\OrderFlow\Plugin\Adminhtml;

use Magento\Framework\AuthorizationInterface;
use Magento\Sales\Block\Adminhtml\Order\View;

/**
 * Adds an Export Button to Order View.
 */
class OrderView
{
    const ACL_RESOURCE = 'RealtimeDespatch_OrderFlow::orderflow_exports_orders';
    const EXPORT_URL_PATH = 'orderflow/order/export';

    /**
     * @var AuthorizationInterface
     */
    protected $auth;

    /**
     * @param AuthorizationInterface $auth
     */
    public function __construct(AuthorizationInterface $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param View $view
     */
    public function beforeSetLayout(View $view)
    {
        if (! $this->auth->isAllowed(self::ACL_RESOURCE)) {
            return;
        }

        $exportUrl = $view->getUrl(self::EXPORT_URL_PATH);
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

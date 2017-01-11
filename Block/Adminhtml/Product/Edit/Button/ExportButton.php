<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Product\Edit\Button;

use Magento\Catalog\Block\Adminhtml\Product\Edit\Button\Generic;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Context;

/**
 * Class ExportButton
 * @package RealtimeDespatch\OrderFlow\Block\Adminhtml\Product\Edit\Button
 */
class ExportButton extends Generic
{
    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $auth;

    /**
     * Generic constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param \Magento\Framework\AuthorizationInterface $auth
     */
    public function __construct(
        Context $context,
        Registry $registry,
        \Magento\Framework\AuthorizationInterface $auth
    ) {
        $this->context = $context;
        $this->registry = $registry;
        $this->auth = $auth;

        parent::__construct($context, $registry);
    }

    public function getButtonData()
    {
        if ( ! $this->auth->isAllowed('RealtimeDespatch_OrderFlow::orderflow_exports_products')) {
            return;
        }

        $storeId = $this->context->getFilterParam('store_id');
        $exportUrl = $this->getUrl(
            'orderflow/product/export',
            ['id' => $this->getProduct()->getId(), 'store' => $storeId]
        );

        $message = __('Are you sure you wish to export this product?');

        return [
            'id' => 'product-view-export-button',
            'label' => __('Export'),
            'on_click' => "confirmSetLocation('{$message}', '{$exportUrl}')",
            'sort_order' => 10
        ];
    }
}
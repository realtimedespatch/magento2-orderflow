<?php

namespace RealtimeDespatch\OrderFlow\Plugin\Adminhtml;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\UrlInterface;

/**
 * Class OrderActions
 */
class OrderActions
{
    protected $urlBuilder;
    protected $context;
    protected $helper;
    protected $auth;

    public function __construct(
        ContextInterface $context,
        UrlInterface $urlBuilder,
        \RealtimeDespatch\OrderFlow\Helper\Export\Order $helper,
        \Magento\Framework\AuthorizationInterface $auth
    )
    {
        $this->urlBuilder = $urlBuilder;
        $this->context = $context;
        $this->helper = $helper;
        $this->auth = $auth;
    }

    /**
     * Adds the export action to the order grid.
     *
     * @param $orderActions
     * @param $result
     * @return mixed
     */
    public function afterPrepareDataSource($orderActions, $result)
    {
        if (isset($result['data']['items'])) {
            $storeId = $this->context->getFilterParam('store_id');
            foreach ($result['data']['items'] as &$item) {
                if ( ! $this->auth->isAllowed('RealtimeDespatch_OrderFlow::orderflow_exports_products')) {
                    continue;
                }

                $item[$orderActions->getData('name')]['export'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'orderflow/order/export',
                        ['order_id' => $item['entity_id'], 'store' => $storeId]
                    ),
                    'label' => __('Export'),
                    'hidden' => false,
                ];
            }
        }

        return $result;
    }
}
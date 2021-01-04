<?php

namespace RealtimeDespatch\OrderFlow\Plugin\Adminhtml;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Sales\Ui\Component\Listing\Column\ViewAction;

/**
 * Adds an Export Button to Order Actions.
 */
class OrderActions
{
    const ACL_RESOURCE = 'RealtimeDespatch_OrderFlow::orderflow_exports_products';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var ContextInterface
     */
    protected $context;

    /**
     * @var AuthorizationInterface
     */
    protected $auth;

    /**
     * @param ContextInterface $context
     * @param UrlInterface $urlBuilder
     * @param AuthorizationInterface $auth
     */
    public function __construct(
        ContextInterface $context,
        UrlInterface $urlBuilder,
        AuthorizationInterface $auth
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->context = $context;
        $this->auth = $auth;
    }

    /**
     * Adds the export action to the order grid.
     *
     * @param ViewAction $orderActions
     * @param array $result
     * @return array
     */
    public function afterPrepareDataSource(ViewAction $orderActions, array $result): array
    {
        if (isset($result['data']['items'])) {
            $storeId = $this->context->getFilterParam('store_id');
            foreach ($result['data']['items'] as &$item) {
                if (! $this->auth->isAllowed(self::ACL_RESOURCE)) {
                    continue;
                }

                $item[$orderActions->getData('name')]['export'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'orderflow/order/export',
                        ['order_id' => $item['entity_id'], 'store' => $storeId]
                    ),
                    'label' => __('Export')
                ];
            }
        }

        return $result;
    }
}

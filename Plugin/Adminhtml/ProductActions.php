<?php

namespace RealtimeDespatch\OrderFlow\Plugin\Adminhtml;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\UrlInterface;

/**
 * Adds an Export Button to Product Actions.
 */
class ProductActions
{
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

    public function __construct(
        ContextInterface $context,
        UrlInterface $urlBuilder,
        AuthorizationInterface $auth
    )
    {
        $this->urlBuilder = $urlBuilder;
        $this->context = $context;
        $this->auth = $auth;
    }

    /**
     * Adds the export action to the product grid.
     *
     * @param $productActions
     * @param $result
     * @return mixed
     */
    public function afterPrepareDataSource($productActions, $result)
    {
        if (isset($result['data']['items'])) {
            $storeId = $this->context->getFilterParam('store_id');
            foreach ($result['data']['items'] as &$item) {
                if ( ! $this->auth->isAllowed('RealtimeDespatch_OrderFlow::orderflow_exports_products')) {
                    continue;
                }

                $item[$productActions->getData('name')]['export'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'orderflow/product/export',
                        ['id' => $item['entity_id'], 'store' => $storeId]
                    ),
                    'label' => __('Export'),
                    'hidden' => false,
                ];
            }
        }

        return $result;
    }
}

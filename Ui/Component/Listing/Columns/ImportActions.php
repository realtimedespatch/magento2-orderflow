<?php

namespace RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class ImportActions extends Column
{
    /** Url path */
    const IMPORT_URL_PATH_VIEW = 'orderflow/import/view';
    const IMPORT_URL_PATH_EDIT = 'orderflow/import/edit';
    const REQUEST_URL_PATH_VIEW = 'orderflow/request/view';

    /** @var UrlInterface */
    protected $urlBuilder;

    /**
     * @var string
     */
    private $editUrl;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $auth;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param \Magento\Framework\AuthorizationInterface $auth,
     * @param array $components
     * @param array $data
     * @param string $editUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \Magento\Framework\AuthorizationInterface $auth,
        array $components = [],
        array $data = [],
        $editUrl = self::IMPORT_URL_PATH_EDIT
    ) {
        $this->auth = $auth;
        $this->urlBuilder = $urlBuilder;
        $this->editUrl = $editUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['import_id'])) {
                    $item[$name]['view_import'] = [
                        'href'  => $this->urlBuilder->getUrl(self::IMPORT_URL_PATH_VIEW, ['import_id' => $item['import_id']]),
                        'label' => __('View Import')
                    ];
                }

                if ($this->canViewRequest()) {
                    $item[$name]['view_request'] = [
                        'href'  => $this->urlBuilder->getUrl(self::REQUEST_URL_PATH_VIEW, ['request_id' => $item['request_id']]),
                        'label' => __('View Processed Request')
                    ];
                }
            }
        }

        return $dataSource;
    }

    /**
     * Checks whether the current user is able to review the associated import request.
     *
     * @return bool
     */
    public function canViewRequest()
    {
        return $this->auth->isAllowed('RealtimeDespatch_OrderFlow::orderflow_requests_imports');
    }
}
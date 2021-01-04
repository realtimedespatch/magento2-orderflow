<?php

namespace RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ExportInterface;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Export;

class RequestExportActions extends Column
{
    /** Url path */
    const REQUEST_URL_PATH_VIEW = 'orderflow/request/view';
    const REQUEST_URL_PATH_EDIT = 'orderflow/request/edit';
    const REQUEST_URL_PATH_PROCESS = 'orderflow/request/process';
    const EXPORT_URL_PATH_VIEW = 'orderflow/export/view';

    /** @var UrlInterface */
    protected $urlBuilder;

    /**
     * @var string
     */
    private $editUrl;

    /**
     * @var Export
     */
    protected $resourceModel;

    /**
     * @var AuthorizationInterface
     */
    protected $auth;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param AuthorizationInterface $auth
     * @param Export $resourceModel
     * @param array $components
     * @param array $data
     * @param string $editUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        AuthorizationInterface $auth,
        Export $resourceModel,
        array $components = [],
        array $data = [],
        $editUrl = self::REQUEST_URL_PATH_EDIT
    ) {
        $this->auth = $auth;
        $this->urlBuilder = $urlBuilder;
        $this->editUrl = $editUrl;
        $this->resourceModel = $resourceModel;
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
                $exportName = $this->getData('name');
                if (isset($item['request_id'])) {
                    $item[$exportName]['view'] = [
                        'href'  => $this->urlBuilder->getUrl(
                            self::REQUEST_URL_PATH_VIEW,
                            ['request_id' => $item['request_id']]
                        ),
                        'label' => __('View Request')
                    ];
                }

                if (isset($item['processed_at']) && $item['processed_at'] == 'Pending') {
                    $item[$exportName]['process'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::REQUEST_URL_PATH_PROCESS,
                            ['request_id' => $item['request_id']]
                        ),
                        'label' => __('Process'),
                        'confirm' => [
                            'title' => __('Process Request'),
                            'message' => __('Are you sure you wish to process this request?')
                        ]
                    ];
                }

                $exportId = $this->resourceModel->getIdByRequestId($item['request_id']);

                if ($exportId && $this->canViewExport($item['entity'])) {
                    $item[$exportName]['view_export'] = [
                        'href'  => $this->urlBuilder->getUrl(self::EXPORT_URL_PATH_VIEW, ['export_id' => $exportId]),
                        'label' => __('View Export Report')
                    ];
                }
            }
        }

        return $dataSource;
    }

    /**
     * Checks whether the current user is able to review the associated export.
     *
     * @param string $entityType
     *
     * @return bool
     */
    public function canViewExport(string $entityType)
    {
        if ($entityType == ExportInterface::ENTITY_PRODUCT) {
            return $this->auth->isAllowed('RealtimeDespatch_OrderFlow::orderflow_exports_products');
        }

        if ($entityType == ExportInterface::ENTITY_ORDER) {
            return $this->auth->isAllowed('RealtimeDespatch_OrderFlow::orderflow_exports_orders');
        }

        return false;
    }
}

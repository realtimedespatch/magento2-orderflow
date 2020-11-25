<?php

namespace RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ImportInterface;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Import;

class RequestImportActions extends Column
{
    /** Url path */
    const REQUEST_URL_PATH_VIEW = 'orderflow/request/view';
    const REQUEST_URL_PATH_EDIT = 'orderflow/request/edit';
    const REQUEST_URL_PATH_PROCESS = 'orderflow/request/process';
    const EXPORT_URL_PATH_VIEW = 'orderflow/import/view';

    /** @var UrlInterface */
    protected $urlBuilder;

    /**
     * @var string
     */
    private $editUrl;

    /**
     * @var Import
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
     * @param Import $resourceModel
     * @param array $components
     * @param array $data
     * @param string $editUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        AuthorizationInterface $auth,
        Import $resourceModel,
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
                $importName = $this->getData('name');
                if (isset($item['request_id'])) {
                    $item[$importName]['view'] = [
                        'href'  => $this->urlBuilder->getUrl(self::REQUEST_URL_PATH_VIEW, ['request_id' => $item['request_id']]),
                        'label' => __('View Request')
                    ];
                }

                if (isset($item['processed_at']) && $item['processed_at'] == 'Pending') {
                    $item[$importName]['process'] = [
                        'href' => $this->urlBuilder->getUrl(self::REQUEST_URL_PATH_PROCESS, ['request_id' => $item['request_id']]),
                        'label' => __('Process'),
                        'confirm' => [
                            'title' => __('Process Request'),
                            'message' => __('Are you sure you wish to process this request?')
                        ]
                    ];
                }

                $importId = $this->resourceModel->getIdByRequestId($item['request_id']);

                if ($importId && $this->canViewImport($item['entity'])) {
                    $item[$importName]['view_import'] = [
                        'href'  => $this->urlBuilder->getUrl(self::EXPORT_URL_PATH_VIEW, ['import_id' => $importId]),
                        'label' => __('View Import Report')
                    ];
                }
            }
        }

        return $dataSource;
    }

    /**
     * Checks whether the current user is able to review the associated import.
     *
     * @param string $entity Entity Type
     *
     * @return bool
     */
    public function canViewImport(string $entity)
    {
        if ($entity == ImportInterface::ENTITY_INVENTORY) {
            return $this->auth->isAllowed('RealtimeDespatch_OrderFlow::orderflow_imports_inventory');
        }

        if ($entity == ImportInterface::ENTITY_SHIPMENT) {
            return $this->auth->isAllowed('RealtimeDespatch_OrderFlow::orderflow_imports_shipments');
        }

        return false;
    }
}

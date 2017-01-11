<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Request;

/**
 * Adminhtml request view
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class View extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Block group
     *
     * @var string
     */
    protected $_blockGroup = 'RealtimeDespatch_OrderFlow';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \RealtimeDespatch\OrderFlow\Model\ResourceModel\Export
     */
    protected $_exportResourceModel;

    /**
     * @var \RealtimeDespatch\OrderFlow\Model\ResourceModel\Import
     */
    protected $_importResourceModel;

    /**
     * View constructor.
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     * @param \Magento\Framework\Registry $registry
     * @param \RealtimeDespatch\OrderFlow\Model\ResourceModel\Export $exportResourceModel
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        array $data = [],
        \Magento\Framework\Registry $registry,
        \RealtimeDespatch\OrderFlow\Model\ResourceModel\Export $exportResourceModel,
        \RealtimeDespatch\OrderFlow\Model\ResourceModel\Import $importResourceModel
    ) {
        $this->_coreRegistry = $registry;
        $this->_exportResourceModel = $exportResourceModel;
        $this->_importResourceModel = $importResourceModel;
        parent::__construct($context, $data);
    }

    /**
     * Constructor
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _construct()
    {
        $this->_objectId = 'request_id';
        $this->_controller = 'adminhtml_request';
        $this->_mode = 'view';

        parent::_construct();

        $request = $this->getRequest();

        if ($request->canProcess()) {
            $this->_setProcessButton($request);
        }

        if ($request->isExport()) {
            $this->_setExportViewButton($request);
        } else {
            $this->_setImportViewButton($request);
        }

        $this->buttonList->remove('delete');
        $this->buttonList->remove('reset');
        $this->buttonList->remove('save');
        $this->setId('orderflow_request_view');
    }

    /**
     * Sets the process button.
     */
    protected function _setProcessButton($request)
    {
        $exportUrl = $this->getUrl(
            'orderflow/request/process',
            ['request_id' => $request->getId()]
        );

        $message = __('Are you sure you wish to process this request?');

        $this->addButton(
        'request_process',
        [
            'label' => __('Process'),
            'class' => 'process',
            'id' => 'request-view-process-button',
            'onclick' => "confirmSetLocation('{$message}', '{$exportUrl}')",
                'sort_order' => 0
            ]
        );
    }

    /**
     * Sets the view import button
     *
     * @param $request
     */
    protected function _setImportViewButton($request)
    {
        $importId = $this->_importResourceModel->getIdByRequestId($request->getId());

        if ( ! $importId || ! $this->canViewImport($request->getEntity())) {
            return;
        }

        $importUrl = $this->getUrl(
            'orderflow/import/view',
            ['import_id' => $importId]
        );

        $this->addButton(
            'import_vew',
            [
                'label' => __('View Import Report'),
                'class' => 'import',
                'id' => 'request-view-import-button',
                'onclick' => 'setLocation(\'' . $importUrl . '\')',
                'sort_order' => 0
            ]
        );
    }

    /**
     * Sets the view export button
     *
     * @param $request
     */
    protected function _setExportViewButton($request)
    {
        $exportId = $this->_exportResourceModel->getIdByRequestId($request->getId());

        if ( ! $exportId || ! $this->canViewExport($request->getEntity())) {
            return;
        }

        $exportUrl = $this->getUrl(
            'orderflow/export/view',
            ['export_id' => $exportId]
        );

        $this->addButton(
            'export_vew',
            [
                'label' => __('View Export Report'),
                'class' => 'export',
                'id' => 'request-view-export-button',
                'onclick' => 'setLocation(\'' . $exportUrl . '\')',
                'sort_order' => 0
            ]
        );
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('orderflow/request/index/type/'.strtolower($this->getRequest()->getType()));
    }

    /**
     * Retrieve available request
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getRequest()
    {
        if ($this->hasRequest()) {
            return $this->getData('request');
        }
        if ($this->_coreRegistry->registry('current_request')) {
            return $this->_coreRegistry->registry('current_request');
        }
        if ($this->_coreRegistry->registry('request')) {
            return $this->_coreRegistry->registry('request');
        }
        throw new \Magento\Framework\Exception\LocalizedException(__('Request Not Found'));
    }

    /**
     * Checks whether the current user is able to review the associated export.
     *
     * @param string $entity Entity Type
     *
     * @return bool
     */
    public function canViewExport($entity)
    {
        if ($entity == \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface::ENTITY_PRODUCT) {
            return $this->_authorization->isAllowed('RealtimeDespatch_OrderFlow::orderflow_exports_products');
        }

        if ($entity == \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface::ENTITY_ORDER) {
            return $this->_authorization->isAllowed('RealtimeDespatch_OrderFlow::orderflow_exports_orders');
        }

        return false;
    }

    /**
     * Checks whether the current user is able to review the associated import.
     *
     * @param string $entity Entity Type
     *
     * @return bool
     */
    public function canViewImport($entity)
    {
        if ($entity == \RealtimeDespatch\OrderFlow\Api\Data\ImportInterface::ENTITY_INVENTORY) {
            return $this->_authorization->isAllowed('RealtimeDespatch_OrderFlow::orderflow_imports_inventory');
        }

        if ($entity == \RealtimeDespatch\OrderFlow\Api\Data\ImportInterface::ENTITY_SHIPMENT) {
            return $this->_authorization->isAllowed('RealtimeDespatch_OrderFlow::orderflow_imports_shipments');
        }

        return false;
    }
}
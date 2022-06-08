<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Export;

/**
 * Adminhtml export view
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
     * @param Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
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
        $this->_objectId = 'export_id';
        $this->_controller = 'adminhtml_export';
        $this->_mode = 'view';

        parent::_construct();

        $export = $this->getExport();

        if ($export->getRequestId() && $this->canViewRequest()) {
            $requestUrl = $this->getUrl(
                'orderflow/request/view',
                ['request_id' => $export->getRequestId()]
            );

            $this->addButton(
                'request_view',
                [
                    'label' => __('View Processed Request'),
                    'class' => 'process',
                    'id' => 'export-view-request-button',
                    'onclick' => 'setLocation(\'' . $requestUrl . '\')',
                    'sort_order' => 0
                ]
            );
        }

        $this->buttonList->remove('delete');
        $this->buttonList->remove('reset');
        $this->buttonList->remove('save');
        $this->setId('orderflow_export_view');
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('orderflow/export/index/type/'.strtolower($this->getExport()->getEntity()));
    }

    /**
     * Retrieve available export
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getExport()
    {
        if ($this->hasExport()) {
            return $this->getData('export');
        }
        if ($this->_coreRegistry->registry('current_export')) {
            return $this->_coreRegistry->registry('current_export');
        }
        if ($this->_coreRegistry->registry('export')) {
            return $this->_coreRegistry->registry('export');
        }
        throw new \Magento\Framework\Exception\LocalizedException(__('Export Not Found.'));
    }

    /**
     * Checks whether the current user is able to review the associated export request.
     *
     * @return bool
     */
    public function canViewRequest()
    {
        return $this->_authorization->isAllowed('RealtimeDespatch_OrderFlow::orderflow_requests_exports');
    }
}

<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Export;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use RealtimeDespatch\OrderFlow\Api\Data\ExportInterface;

/**
 * Adminhtml export view
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class View extends Container
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
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
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
     * @throws LocalizedException
     * @throws LocalizedException
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

        /** @noinspection PhpUndefinedMethodInspection */
        $this->setId('orderflow_export_view');
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     * @throws LocalizedException
     * @throws LocalizedException
     */
    public function getBackUrl()
    {
        return $this->getUrl('orderflow/export/index/type/'.strtolower($this->getExport()->getEntity()));
    }

    /**
     * Retrieve available export
     *
     * @return ExportInterface
     * @throws LocalizedException
     */
    public function getExport()
    {
        if ($this->getData('export')) {
            return $this->getData('export');
        }
        if ($this->_coreRegistry->registry('current_export')) {
            return $this->_coreRegistry->registry('current_export');
        }
        if ($this->_coreRegistry->registry('export')) {
            return $this->_coreRegistry->registry('export');
        }
        throw new LocalizedException(__('Export Not Found.'));
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

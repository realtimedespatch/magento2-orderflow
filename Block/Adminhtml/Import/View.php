<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Import;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use RealtimeDespatch\OrderFlow\Api\Data\ImportInterface;

/**
 * Adminhtml import view
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
        $this->_objectId = 'import_id';
        $this->_controller = 'adminhtml_import';
        $this->_mode = 'view';

        parent::_construct();

        $import = $this->getImport();

        if ($import->getRequestId() && $this->canViewRequest()) {
            $requestUrl = $this->getUrl(
                'orderflow/request/view',
                ['request_id' => $import->getRequestId()]
            );

            $this->addButton(
                'request_view',
                [
                    'label' => __('View Processed Request'),
                    'class' => 'process',
                    'id' => 'import-view-request-button',
                    'onclick' => 'setLocation(\'' . $requestUrl . '\')',
                    'sort_order' => 0
                ]
            );
        }

        $this->buttonList->remove('delete');
        $this->buttonList->remove('reset');
        $this->buttonList->remove('save');

        /** @noinspection PhpUndefinedMethodInspection */
        $this->setId('orderflow_import_view');
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
        return $this->getUrl('orderflow/import/index/type/'.strtolower($this->getImport()->getEntity()));
    }

    /**
     * Retrieve available import
     *
     * @return ImportInterface
     * @throws LocalizedException
     */
    public function getImport()
    {
        if ($this->getData('import')) {
            return $this->getData('import');
        }
        if ($this->_coreRegistry->registry('current_import')) {
            return $this->_coreRegistry->registry('current_import');
        }
        if ($this->_coreRegistry->registry('import')) {
            return $this->_coreRegistry->registry('import');
        }
        throw new LocalizedException(__('Import Not Found.'));
    }

    /**
     * Checks whether the current user is able to review the associated import request.
     *
     * @return bool
     */
    public function canViewRequest()
    {
        return $this->_authorization->isAllowed('RealtimeDespatch_OrderFlow::orderflow_requests_imports');
    }
}

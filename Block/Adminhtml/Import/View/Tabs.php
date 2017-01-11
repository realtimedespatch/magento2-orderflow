<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Import\View;

/**
 * Import View Tabs
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    /**
     * Retrieve available import
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getImport()
    {
        if ($this->hasImport()) {
            return $this->getData('import');
        }
        if ($this->_coreRegistry->registry('current_import')) {
            return $this->_coreRegistry->registry('current_import');
        }
        if ($this->_coreRegistry->registry('import')) {
            return $this->_coreRegistry->registry('import');
        }
        throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t get the import instance right now.'));
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('orderflow_import_view_tabs');
        $this->setDestElementId('orderflow_import_view');
        $this->setTitle(__('Import View'));
    }
}
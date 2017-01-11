<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Import;

/**
 * Adminhtml import abstract block
 */
class AbstractImport extends \Magento\Backend\Block\Widget
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
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
        throw new \Magento\Framework\Exception\LocalizedException(__('Import Not Found.'));
    }
}
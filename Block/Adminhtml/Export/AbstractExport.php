<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Export;

/**
 * Adminhtml export abstract block
 */
class AbstractExport extends \Magento\Backend\Block\Widget
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var Magento\Store\Model\WebsiteFactory
     */
    protected $_websiteFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_websiteFactory = $websiteFactory;
        parent::__construct($context, $data);
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
}
<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Export;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Store\Model\WebsiteFactory;
use RealtimeDespatch\OrderFlow\Api\Data\ExportInterface;

/**
 * Adminhtml export abstract block
 */
class AbstractExport extends Widget
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var WebsiteFactory
     */
    protected $_websiteFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param WebsiteFactory $websiteFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        WebsiteFactory $websiteFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_websiteFactory = $websiteFactory;
        parent::__construct($context, $data);
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
}

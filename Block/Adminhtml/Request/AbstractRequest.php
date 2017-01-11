<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Request;

/**
 * Adminhtml request abstract block
 */
class AbstractRequest extends \Magento\Backend\Block\Widget
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
        throw new \Magento\Framework\Exception\LocalizedException(__('Request Not Found.'));
    }
}
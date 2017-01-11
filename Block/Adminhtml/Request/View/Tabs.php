<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\View;

/**
 * Request View Tabs
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

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('orderflow_request_view_tabs');
        $this->setDestElementId('orderflow_request_view');
        $this->setTitle(__('Request View'));
    }
}
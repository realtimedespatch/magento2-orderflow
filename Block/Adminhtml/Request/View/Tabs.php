<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\View;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;

/**
 * Request View Tabs
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Context $context
     * @param EncoderInterface $jsonEncoder
     * @param Session $authSession
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        EncoderInterface $jsonEncoder,
        Session $authSession,
        Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    /**
     * Retrieve available request
     *
     * @return RequestInterface
     * @throws LocalizedException
     */
    public function getRequest()
    {
        if ($this->getData('request')) {
            return $this->getData('request');
        }
        if ($this->_coreRegistry->registry('current_request')) {
            return $this->_coreRegistry->registry('current_request');
        }
        if ($this->_coreRegistry->registry('request')) {
            return $this->_coreRegistry->registry('request');
        }
        throw new LocalizedException(__('Request Not Found.'));
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        /** @noinspection PhpUndefinedMethodInspection */
        $this->setId('orderflow_request_view_tabs');
        /** @noinspection PhpUndefinedMethodInspection */
        $this->setTitle(__('Request View'));
        $this->setDestElementId('orderflow_request_view');
    }
}

<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\View\Tab\Request;

/**
 * Request Body Tab.
 */
class Body extends \RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\AbstractRequest implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Retrieve source model instance
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     */
    public function getSource()
    {
        return $this->getRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Request Body');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Request Body');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return ! $this->getRequest()->isExport() || $this->getRequest()->getOperation() == 'Export';
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return $this->getRequest()->isExport() && $this->getRequest()->getOperation() !== 'Export';
    }
}

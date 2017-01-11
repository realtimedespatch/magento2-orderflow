<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\View\Tab\Response;

/**
 * Response Body Tab.
 */
class Body extends \RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\AbstractRequest implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Retrieve source model instance
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ResponseInterface
     */
    public function getSource()
    {
        return $this->getResponse();
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Response Body');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Response Body');
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

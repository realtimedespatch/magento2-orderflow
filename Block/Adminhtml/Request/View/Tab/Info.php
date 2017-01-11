<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\View\Tab;

/**
 * Request Info Tab.
 */
class Info extends \RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\AbstractRequest implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
     * Returns the associated website name.
     *
     * @return string
     */
    public function getWebsiteName()
    {
        if ( ! $this->getRequest()->getScopeId()) {
            return 'OrderFlow';
        }

        $website = $this->_websiteFactory
                        ->create()
                        ->load($this->getRequest()->getScopeId());

        return $website->getName();
    }

    /**
     * Get items html
     *
     * @return string
     */
    public function getItemsHtml()
    {
        return $this->getChildHtml('request_items');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}

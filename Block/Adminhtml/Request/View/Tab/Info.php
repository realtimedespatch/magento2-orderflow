<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\View\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\AbstractRequest;

/**
 * Request Info Tab.
 */
class Info extends AbstractRequest implements TabInterface
{
    /**
     * Returns the associated website name.
     *
     * @return string
     */
    public function getWebsiteName()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        if (! $this->getRequest()->getScopeId()) {
            return 'OrderFlow';
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $website = $this->websiteFactory
                        ->create()
                        ->load($this->getRequest()->getScopeId());

        return $website->getName();
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

    /**
     * Is Ajax Loaded.
     */
    public function isAjaxLoaded()
    {
        return false;
    }
}

<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\View\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Exception\NoSuchEntityException;
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
     * @throws NoSuchEntityException
     */
    public function getWebsiteName()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        if (! $this->getRtdRequest()->getScopeId()) {
            return 'OrderFlow';
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $website = $this->websiteFactory
                        ->create()
                        ->load($this->getRtdRequest()->getScopeId());

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

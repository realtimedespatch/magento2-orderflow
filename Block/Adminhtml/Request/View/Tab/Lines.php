<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\View\Tab;

/**
 * Request Info Tab.
 */
class Lines extends \Magento\Framework\View\Element\Text\ListText implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Request Lines');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Request Lines');
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
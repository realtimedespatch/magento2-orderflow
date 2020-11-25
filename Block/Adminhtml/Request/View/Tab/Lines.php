<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\View\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\View\Element\Text\ListText;

/**
 * Request Info Tab.
 */
class Lines extends ListText implements TabInterface
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

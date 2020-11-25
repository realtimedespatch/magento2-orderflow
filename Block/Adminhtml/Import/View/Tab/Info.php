<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Import\View\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use RealtimeDespatch\OrderFlow\Block\Adminhtml\Import\AbstractImport;

class Info extends AbstractImport implements TabInterface
{
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

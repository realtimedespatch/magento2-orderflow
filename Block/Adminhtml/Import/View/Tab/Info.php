<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Import\View\Tab;

/**
 * Import Info Tab.
 */
class Info extends \RealtimeDespatch\OrderFlow\Block\Adminhtml\Import\AbstractImport implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Retrieve source model instance
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportInterface
     */
    public function getSource()
    {
        return $this->getImport();
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

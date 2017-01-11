<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Import\View\Tab;

/**
 * Import Info Tab.
 */
class Lines extends \RealtimeDespatch\OrderFlow\Block\Adminhtml\Import\AbstractImport  implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
     * Get items html
     *
     * @return string
     */
    public function getItemsHtml()
    {
        return $this->getChildHtml('lines');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Import Lines');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Import Lines');
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
<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Export\View\Tab;

/**
 * Export Info Tab.
 */
class Lines extends \RealtimeDespatch\OrderFlow\Block\Adminhtml\Export\AbstractExport  implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Retrieve source model instance
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     */
    public function getSource()
    {
        return $this->getExport();
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
        return __('Export Lines');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Export Lines');
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
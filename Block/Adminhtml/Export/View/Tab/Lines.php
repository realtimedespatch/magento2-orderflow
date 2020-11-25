<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Export\View\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Exception\LocalizedException;
use RealtimeDespatch\OrderFlow\Api\Data\ExportInterface;
use RealtimeDespatch\OrderFlow\Block\Adminhtml\Export\AbstractExport;

/**
 * Export Info Tab.
 */
class Lines extends AbstractExport  implements TabInterface
{
    /**
     * Retrieve source model instance
     *
     * @return ExportInterface
     * @throws LocalizedException
     * @throws LocalizedException
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

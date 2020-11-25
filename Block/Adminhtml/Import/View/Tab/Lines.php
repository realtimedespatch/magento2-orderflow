<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Import\View\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Exception\LocalizedException;
use RealtimeDespatch\OrderFlow\Api\Data\ImportInterface;
use RealtimeDespatch\OrderFlow\Block\Adminhtml\Import\AbstractImport;

/**
 * Import Info Tab.
 */
class Lines extends AbstractImport  implements TabInterface
{
    /**
     * Retrieve source model instance
     *
     * @return ImportInterface
     * @throws LocalizedException
     * @throws LocalizedException
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

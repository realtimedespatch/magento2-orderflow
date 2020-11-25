<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Import\View;

use Magento\Framework\Exception\LocalizedException;
use RealtimeDespatch\OrderFlow\Block\Adminhtml\Import\AbstractImport;

class InventoryInfo extends AbstractImport
{
    /**
     * Only Render for Inventory Imports.
     *
     * @return string
     */
    public function toHtml()
    {
        try {
            if ($this->getImport()->getEntity() !== 'Inventory') {
                return '';
            }
        } catch (LocalizedException $ex) {
            return '';
        }

        return parent::toHtml();
    }
}

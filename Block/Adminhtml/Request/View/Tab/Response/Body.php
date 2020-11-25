<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\View\Tab\Response;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Exception\LocalizedException;
use RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\AbstractRequest;

/**
 * Response Body Tab.
 */
class Body extends AbstractRequest implements TabInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Response Body');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Response Body');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        try {
            return ! $this->getRtdRequest()->isExport() || $this->getRtdRequest()->getOperation() == 'Export';
        } catch (LocalizedException $ex) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        try {
            return $this->getRtdRequest()->isExport() && $this->getRtdRequest()->getOperation() !== 'Export';
        } catch (LocalizedException $ex) {
            return true;
        }
    }

    /**
     * Is Ajax Loaded.
     */
    public function isAjaxLoaded()
    {
        return false;
    }
}

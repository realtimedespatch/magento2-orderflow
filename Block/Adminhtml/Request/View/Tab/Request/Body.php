<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\View\Tab\Request;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Exception\LocalizedException;
use RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\AbstractRequest;

/**
 * Request Body Tab.
 */
class Body extends AbstractRequest implements TabInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Request Body');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Request Body');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        try {
            $request = $this->getRtdRequest();

            return ! $request->isExport() || $request->getOperation() == 'Export';
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
            $request = $this->getRtdRequest();

            return $request->isExport() && $request->getOperation() !== 'Export';
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

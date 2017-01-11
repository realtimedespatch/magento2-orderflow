<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Export\View\Tab;

/**
 * Export Info Tab.
 */
class Info extends \RealtimeDespatch\OrderFlow\Block\Adminhtml\Export\AbstractExport implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
     * Returns the associated website name.
     *
     * @return string
     */
    public function getWebsiteName()
    {
        if ( ! $this->getExport()->getScopeId()) {
            return 'OrderFlow';
        }

        $website = $this->_websiteFactory
            ->create()
            ->load($this->getExport()->getScopeId());

        return $website->getName();
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

<?php

namespace RealtimeDespatch\OrderFlow\Helper\Admin;

/**
 * Class Info
 * @package RealtimeDespatch\OrderFlow\Helper\Admin
 */
class Info extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Checks whether information can be displayed within the admin interfaces.
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            'orderflow_admin_info/settings/is_enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }
}
<?php

namespace RealtimeDespatch\OrderFlow\Helper\Admin;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Info extends AbstractHelper
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
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}

<?php

namespace RealtimeDespatch\OrderFlow\Controller\Adminhtml\Export;

use Magento\Backend\Model\View\Result\Page;
use RealtimeDespatch\OrderFlow\Controller\Adminhtml\Export;

class Index extends Export
{
    const EXPORT_TYPE_ORDER = 'Order';
    const EXPORT_LISTING_ORDER = 'order_export_listing';
    const EXPORT_LISTING_PRODUCT = 'product_export_listing';

    /**
     * Execute.
     *
     * @return Page
     */
    public function execute()
    {
        $page = $this->getPage();
        $exportType = ucfirst($this->getRequest()->getParam('type'));

        $page->getConfig()->getTitle()->prepend(__($exportType. ' Exports'));
        $page->addBreadcrumb(__($exportType), __($exportType));

        $page->getLayout()->unsetChild('content', $this->getListing($exportType));

        return $page;
    }

    /**
     * Listing Getter.
     *
     * @param string $exportType
     * @return string
     */
    protected function getListing(string $exportType)
    {
        if ($exportType == self::EXPORT_TYPE_ORDER) {
            return self::EXPORT_LISTING_PRODUCT;
        }

        return self::EXPORT_LISTING_ORDER;
    }
}

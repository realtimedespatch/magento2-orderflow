<?php

namespace RealtimeDespatch\OrderFlow\Controller\Adminhtml\Import;

use Magento\Backend\Model\View\Result\Page;
use RealtimeDespatch\OrderFlow\Controller\Adminhtml\Import;

class Index extends Import
{
    const IMPORT_TYPE_INVENTORY = 'Inventory';
    const IMPORT_LISTING_SHIPMENT = 'shipment_import_listing';
    const IMPORT_LISTING_INVENTORY = 'inventory_import_listing';

    /**
     * Execute.
     *
     * @return Page
     */
    public function execute()
    {
        $page = $this->getPage();
        $importType = ucfirst($this->getRequest()->getParam('type'));

        $page->getConfig()->getTitle()->prepend(__($importType. ' Imports'));
        $page->addBreadcrumb(__($importType), __($importType));

        $page->getLayout()->unsetChild('content', $this->getListing($importType));

        return $page;
    }

    /**
     * Listing Getter.
     *
     * @param string $importType
     * @return string
     */
    protected function getListing(string $importType)
    {
        if ($importType == self::IMPORT_TYPE_INVENTORY) {
            return self::IMPORT_LISTING_SHIPMENT;
        }

        return self::IMPORT_LISTING_INVENTORY;
    }
}

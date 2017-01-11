<?php

namespace RealtimeDespatch\OrderFlow\Controller\Adminhtml\Import;

class Index extends \RealtimeDespatch\OrderFlow\Controller\Adminhtml\Import
{
    /**
     * Import Grid
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $importType = ucfirst($this->getRequest()->getParam('type'));

        $resultPage->getConfig()->getTitle()->prepend(__($importType. ' Imports'));
        $resultPage->addBreadcrumb(__($importType), __($importType));

        if ($importType == 'Inventory') {
            $resultPage->getLayout()->unsetChild('content', 'shipment_import_listing');
        } else {
            $resultPage->getLayout()->unsetChild('content', 'inventory_import_listing');
        }

        return $resultPage;
    }
}
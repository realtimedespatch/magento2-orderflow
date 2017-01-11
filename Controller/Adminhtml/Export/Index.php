<?php

namespace RealtimeDespatch\OrderFlow\Controller\Adminhtml\Export;

class Index extends \RealtimeDespatch\OrderFlow\Controller\Adminhtml\Export
{
    /**
     * Export Grid
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $exportType = ucfirst($this->getRequest()->getParam('type'));

        $resultPage->getConfig()->getTitle()->prepend(__($exportType. ' Exports'));
        $resultPage->addBreadcrumb(__($exportType), __($exportType));

        if ($exportType == 'Order') {
            $resultPage->getLayout()->unsetChild('content', 'product_export_listing');
        } else {
            $resultPage->getLayout()->unsetChild('content', 'order_export_listing');
        }

        return $resultPage;
    }
}
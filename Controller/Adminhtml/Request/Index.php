<?php

namespace RealtimeDespatch\OrderFlow\Controller\Adminhtml\Request;

class Index extends \RealtimeDespatch\OrderFlow\Controller\Adminhtml\Request
{
    /**
     * Request Grid
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage  = $this->_initAction();
        $requestType = ucfirst($this->getRequest()->getParam('type'));

        $resultPage->getConfig()->getTitle()->prepend(__($requestType. ' Requests'));

        if ($requestType == 'Export') {
            $resultPage->getLayout()->unsetChild('content', 'import_request_listing');
        } else {
            $resultPage->getLayout()->unsetChild('content', 'export_request_listing');
        }

        return $resultPage;
    }
}
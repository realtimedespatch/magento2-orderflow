<?php

namespace RealtimeDespatch\OrderFlow\Controller\Adminhtml\Request;

use Magento\Backend\App\Action;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;
use Psr\Log\LoggerInterface;

class View extends \RealtimeDespatch\OrderFlow\Controller\Adminhtml\Request
{
    /**
     * View request detail
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $request = $this->_initRequest();
        $resultRedirect = $this->resultRedirectFactory->create();

        if ( ! $request) {
            return $resultRedirect->setRefererUrl();
        }

        try {
            $resultPage = $this->_initAction();
            $resultPage->getConfig()->getTitle()->prepend(sprintf($request->getType()." Request #%s", $request->getMessageId()));

            if ($request->getType() == 'Import') {
                $resultPage->getLayout()->unsetChild('lines', 'request_export_line_listing');
            } else {
                $resultPage->getLayout()->unsetChild('lines', 'request_import_line_listing');
            }
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addError(__('Exception occurred during request load'));
            return $resultRedirect->setRefererUrl();
        }

        return $resultPage;
    }
}
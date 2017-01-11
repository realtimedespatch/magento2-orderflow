<?php

namespace RealtimeDespatch\OrderFlow\Controller\Adminhtml\Export;

use Magento\Backend\App\Action;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;
use Psr\Log\LoggerInterface;

class View extends \RealtimeDespatch\OrderFlow\Controller\Adminhtml\Export
{
    /**
     * View export detail
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $export = $this->_initExport();
        $resultRedirect = $this->resultRedirectFactory->create();

        if ( ! $export) {
            return $resultRedirect->setRefererUrl();
        }

        $export->setViewedAt(date('Y-m-d H:i:s'))->save();

        try {
            $resultPage = $this->_initAction();
            $resultPage->getConfig()->getTitle()->prepend(sprintf($export->getEntity()." Export #%s", $export->getMessageId()));

            if ($export->getEntity() == 'Order') {
                $resultPage->getLayout()->unsetChild('lines', 'product_export_line_listing');
            } else {
                $resultPage->getLayout()->unsetChild('lines', 'order_export_line_listing');
            }
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addError(__('Export Not Found'));
            return $resultRedirect->setRefererUrl();
        }

        return $resultPage;
    }
}
<?php

namespace RealtimeDespatch\OrderFlow\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;
use Psr\Log\LoggerInterface;

class View extends \RealtimeDespatch\OrderFlow\Controller\Adminhtml\Import
{
    /**
     * View import detail
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $import = $this->_initImport();
        $resultRedirect = $this->resultRedirectFactory->create();

        if ( ! $import) {
            return $resultRedirect->setRefererUrl();
        }

        $import->setViewedAt(date('Y-m-d H:i:s'))->save();

        try {
            $resultPage = $this->_initAction();
            $resultPage->getConfig()->getTitle()->prepend(sprintf($import->getEntity()." Import #%s", $import->getMessageId()));

            if ($import->getEntity() == 'Inventory') {
                $resultPage->getLayout()->unsetChild('lines', 'shipment_import_line_listing');
            } else {
                $resultPage->getLayout()->unsetChild('lines', 'inventory_import_line_listing');
            }
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addError(__($e->getMessage()));
            return $resultRedirect->setRefererUrl();
        }

        return $resultPage;
    }
}
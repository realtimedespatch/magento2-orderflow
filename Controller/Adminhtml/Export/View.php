<?php

namespace RealtimeDespatch\OrderFlow\Controller\Adminhtml\Export;

use Exception;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\View\Result\Page;
use RealtimeDespatch\OrderFlow\Controller\Adminhtml\Export;

class View extends Export
{
    /**
     * Execute.
     *
     * @return Page|Redirect
     */
    public function execute()
    {
        try {
            $export = $this->getExport();

            if ( ! $export) {
                return $this->resultRedirectFactory->create()->setRefererUrl();
            }

            $page = $this->getPage();
            $page->getConfig()
                 ->getTitle()
                 ->prepend(sprintf($export->getEntity()." Export #%s", $export->getMessageId()));

            return $page;
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            return $this->resultRedirectFactory->create()->setRefererUrl();
        }
    }
}

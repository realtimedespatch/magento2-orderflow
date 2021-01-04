<?php

namespace RealtimeDespatch\OrderFlow\Controller\Adminhtml\Import;

use Exception;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\Result\Redirect;
use RealtimeDespatch\OrderFlow\Controller\Adminhtml\Import;

class View extends Import
{
    /**
     * Execute.
     *
     * @return Page|Redirect
     */
    public function execute()
    {
        try {
            $import = $this->getImport();

            if (! $import) {
                return $this->resultRedirectFactory->create()->setRefererUrl();
            }

            $page = $this->getPage();
            $page->getConfig()
                 ->getTitle()
                 ->prepend(sprintf($import->getEntity()." Import #%s", $import->getMessageId()));

            return $page;
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            return $this->resultRedirectFactory->create()->setRefererUrl();
        }
    }
}

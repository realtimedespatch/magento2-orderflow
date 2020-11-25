<?php

namespace RealtimeDespatch\OrderFlow\Controller\Adminhtml\Request;

use Exception;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\Result\Redirect;
use RealtimeDespatch\OrderFlow\Controller\Adminhtml\Request;

class View extends Request
{
    /**
     * Execute.
     *
     * @return Page|Redirect
     */
    public function execute()
    {
        try {
            $request = $this->getOrderFlowRequest();

            if ( ! $request) {
                return $this->resultRedirectFactory->create()->setRefererUrl();
            }

            $page = $this->getPage();
            $page->getConfig()
                 ->getTitle()
                 ->prepend(sprintf($request->getType()." Request #%s", $request->getMessageId()));

            return $page;
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            return $this->resultRedirectFactory->create()->setRefererUrl();
        }
    }
}

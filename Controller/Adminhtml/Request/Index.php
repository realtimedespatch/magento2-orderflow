<?php

namespace RealtimeDespatch\OrderFlow\Controller\Adminhtml\Request;

use Magento\Backend\Model\View\Result\Page;
use RealtimeDespatch\OrderFlow\Controller\Adminhtml\Request;

class Index extends Request
{
    const REQUEST_TYPE_EXPORT = 'Export';
    const REQUEST_LISTING_EXPORT = 'export_request_listing';
    const REQUEST_LISTING_IMPORT = 'import_request_listing';

    /**
     * Request Grid
     *
     * @return Page
     */
    public function execute()
    {
        $page = $this->getPage();
        $requestType = ucfirst($this->getRequest()->getParam('type'));

        $page->getConfig()->getTitle()->prepend(__($requestType. ' Requests'));
        $page->addBreadcrumb(__($requestType), __($requestType));

        $page->getLayout()->unsetChild('content', $this->getListing($requestType));

        return $page;
    }

    /**
     * Listing Getter.
     *
     * @param string $requestType
     * @return string
     */
    protected function getListing(string $requestType)
    {
        if ($requestType == self::REQUEST_TYPE_EXPORT) {
            return self::REQUEST_LISTING_IMPORT;
        }

        return self::REQUEST_LISTING_EXPORT;
    }
}

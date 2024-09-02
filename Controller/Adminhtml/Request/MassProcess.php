<?php

namespace RealtimeDespatch\OrderFlow\Controller\Adminhtml\Request;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;

class MassProcess extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory)
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        foreach ($collection->getItems() as $request) {
            $this->_getProcessor($request->getEntity(), $request->getOperation())->process($request);
        }

        $this->messageManager->addSuccess(__('A total of %1 request(s) have been processed.', $collectionSize));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setRefererUrl();
    }

    /**
     * Retrieve the processor instance.
     *
     * @param $type Processor Entity
     * @param $type Processor Operation
     *
     * @return RealtimeDespatch\OrderFlow\Model\Service\Request\RequestProcessor
     */
    protected function _getProcessor($entity, $operation)
    {
        return $this->_objectManager->create($entity.$operation.'RequestProcessor');
    }
}
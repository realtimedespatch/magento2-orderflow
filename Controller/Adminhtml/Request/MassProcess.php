<?php

/** @noinspection PhpUndefinedClassInspection */

namespace RealtimeDespatch\OrderFlow\Controller\Adminhtml\Request;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use RealtimeDespatch\OrderFlow\Api\RequestProcessorFactoryInterface;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request\CollectionFactory;

/**
 * Mass Request Process Controller.
 *
 * Processes a batch of OrderFlow requests.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class MassProcess extends Action
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
     * @var RequestProcessorFactoryInterface
     */
    protected $requestProcessorFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param RequestProcessorFactoryInterface $requestProcessorFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        RequestProcessorFactoryInterface $requestProcessorFactory
    ) {
        parent::__construct($context);

        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->requestProcessorFactory = $requestProcessorFactory;
    }

    /**
     * Execute.
     *
     * @return Redirect
     * @throws LocalizedException
     */
    public function execute()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        foreach ($collection as $request) {
            $this->requestProcessorFactory->get($request->getEntity(), $request->getOperation())->process($request);
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 request(s) have been processed.', $collectionSize));

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setRefererUrl();
    }
}

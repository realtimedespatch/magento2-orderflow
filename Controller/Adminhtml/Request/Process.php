<?php

namespace RealtimeDespatch\OrderFlow\Controller\Adminhtml\Request;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use RealtimeDespatch\OrderFlow\Api\RequestProcessorFactoryInterface;
use \RealtimeDespatch\OrderFlow\Api\RequestRepositoryInterface;
use RealtimeDespatch\OrderFlow\Model\Request;

/**
 * Request Process Controller.
 *
 * Processes an individual OrderFlow request.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Process extends Action
{
    /**
     * @var RequestRepositoryInterface
     */
    protected $requestRepository;

    /**
     * @var RequestProcessorFactoryInterface
     */
    protected $requestProcessorFactory;

    /**
     * @param Context $context
     * @param RequestRepositoryInterface $requestRepository
     * @param RequestProcessorFactoryInterface $requestProcessorFactory
     */
    public function __construct(
        Context $context,
        RequestRepositoryInterface $requestRepository,
        RequestProcessorFactoryInterface $requestProcessorFactory
    ) {
        parent::__construct($context);

        $this->requestRepository = $requestRepository;
        $this->requestProcessorFactory = $requestProcessorFactory;
    }

    /**
     * Execute.
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $requestId = $this->getRequest()->getParam('request_id');
        $resultRedirect = $this->resultRedirectFactory->create();

        if (! $requestId) {
            $this->messageManager->addErrorMessage(__('Cannot retrieve request.'));
            return $resultRedirect->setRefererUrl();
        }

        try {
            /* @var Request $request */
            $request = (int) $this->requestRepository->get($requestId);
            $this->requestProcessorFactory->get($request->getEntity(), $request->getOperation())->process($request);
            $this->messageManager->addSuccessMessage(__('Request successfully processed.'));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setRefererUrl();
    }
}

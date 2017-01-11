<?php

namespace RealtimeDespatch\OrderFlow\Controller\Adminhtml\Request;

use Magento\Backend\App\Action\Context;
use \RealtimeDespatch\OrderFlow\Api\RequestRepositoryInterface;

class Process extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Sales\Api\RequestRepositoryInterface
     */
    protected $_requestRepository;

    /**
     * @param Context $context
     * @param RequestRepositoryInterface $requestRepository
     */
    public function __construct(Context $context, RequestRepositoryInterface $requestRepository)
    {
        parent::__construct($context);
        $this->_requestRepository = $requestRepository;
    }

    /**
     * Import action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('request_id');
        $resultRedirect = $this->resultRedirectFactory->create();

        if ( ! $id) {
            $this->messageManager->addError(__('Request Not Found'));
            return $resultRedirect->setRefererUrl();
        }

        try {
            $request = $this->_requestRepository->get($id);
            $this->_getProcessor($request->getEntity(), $request->getOperation())->process($request);
            $this->messageManager->addSuccess(__('The request has been processed.'));
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

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
<?php

namespace RealtimeDespatch\OrderFlow\Plugin\Webapi\Soap;

use Magento\Webapi\Controller\Soap\Request\Handler;
use Magento\Sales\Model\OrderRepositoryFactory;
use RealtimeDespatch\OrderFlow\Api\RequestRepositoryInterface;
use RealtimeDespatch\OrderFlow\Model\Runtime\OrderRepositoryRefreshContext;

class OrderExport
{
    const OP_ORDER_EXPORT = 'salesOrderRepositoryV1Get';

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface
     */
    protected $_requestBuilder;

    /**
     * @var RequestRepositoryInterface
     */
    protected $_requestRepository;

    protected OrderRepositoryFactory $_orderRepositoryFactory;

    protected OrderRepositoryRefreshContext $_orderRepositoryRefreshContext;

    /**
     * OrderExport constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface $requestBuilder
     * @param RequestRepositoryInterface $requestRepository
     * @param OrderRepositoryFactory $orderRepositoryFactory
     * @param OrderRepositoryRefreshContext $orderRepositoryRefreshContext
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface $requestBuilder,
        RequestRepositoryInterface $requestRepository,
        OrderRepositoryFactory $orderRepositoryFactory,
        OrderRepositoryRefreshContext $orderRepositoryRefreshContext)
    {
        $this->_objectManager = $objectManager;
        $this->_requestBuilder = $requestBuilder;
        $this->_requestRepository = $requestRepository;
        $this->_orderRepositoryFactory = $orderRepositoryFactory;
        $this->_orderRepositoryRefreshContext = $orderRepositoryRefreshContext;
    }

    public function around__call(
        Handler $soapServer,
        callable $proceed,
        $operation,
        $arguments
    ) {
        $request = null;

        if ($this->_isOrderExport($operation) && isset($arguments[0]->id)) {
            $request = $this->_buildOrderExportRequest($arguments[0]->id);
            $this->_getRequestProcessor()->process($request);
        }

        $result = $this->_proceedWithFreshOrderRepository($proceed, $operation, $arguments);

        if ($request !== null) {
            $request->setResponseBody(json_encode($result['result']));
            $this->_requestRepository->save($request);
        }

        return $result;
    }

    /**
     * Checks whether this is a order export request.
     *
     * @param string $operation
     *
     * @return boolean
     */
    protected function _isOrderExport($operation): bool
    {
        return $operation === self::OP_ORDER_EXPORT;
    }

    /**
     * Builds an export request from the order ID.
     *
     * @param string $id
     *
     * @return \RealtimeDespatch\OrderFlow\Model\Request
     */
    protected function _buildOrderExportRequest($id)
    {
        $this->_requestBuilder->setRequestData(
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::TYPE_EXPORT,
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::ENTITY_ORDER,
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::OP_EXPORT
        );

        // Use a fresh repository instance so we do not reuse a stale cached order.
        $order = $this->_orderRepositoryFactory->create()->get((int) $id);

        $this->_requestBuilder->setRequestBody(file_get_contents('php://input'));
        $this->_requestBuilder->addRequestLine(json_encode(array('increment_id' => $order->getIncrementId())));

        return $this->_requestBuilder->saveRequest();
    }

    /**
     * Retrieve the request processor instance.
     *
     * @return RealtimeDespatch\OrderFlow\Model\Service\Request\RequestProcessor
     */
    protected function _getRequestProcessor()
    {
        return $this->_objectManager->create('OrderExportRequestProcessor');
    }

    protected function _proceedWithFreshOrderRepository(callable $proceed, $operation, $arguments)
    {
        if (!$this->_isOrderExport($operation) || !isset($arguments[0]->id)) {
            return $proceed($operation, $arguments);
        }

        return $this->_orderRepositoryRefreshContext->runForOrderId(
            (int) $arguments[0]->id,
            fn () => $proceed($operation, $arguments)
        );
    }
}

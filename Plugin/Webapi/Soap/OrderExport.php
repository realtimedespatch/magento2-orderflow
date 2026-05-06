<?php

namespace RealtimeDespatch\OrderFlow\Plugin\Webapi\Soap;

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
     * @var \Magento\Sales\Model\OrderRepository
     */
    protected $_orderRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * OrderExport constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface $requestBuilder
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface $requestBuilder,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->_objectManager = $objectManager;
        $this->_requestBuilder = $requestBuilder;
        $this->_orderRepository = $orderRepository;
        $this->_storeManager = $storeManager;
    }

    public function around__call(\Magento\Webapi\Controller\Soap\Request\Handler $soapServer, callable $proceed, $operation, $arguments)
    {
        $result = $proceed($operation, $arguments);

        if ($this->_isOrderExport($operation) && isset($arguments[0]->id)) {
            $this->_getRequestProcessor()->process($this->_buildOrderExportRequest($result['result'], $arguments[0]->id));
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
    protected function _isOrderExport($operation)
    {
        return $operation === self::OP_ORDER_EXPORT;
    }

    /**
     * Builds an export request from the order ID.
     *
     * @param string $response
     * @param string $id
     *
     * @return \RealtimeDespatch\OrderFlow\Model\Request
     */
    protected function _buildOrderExportRequest($response, $id)
    {
        $this->_requestBuilder->setRequestData(
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::TYPE_EXPORT,
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::ENTITY_ORDER,
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::OP_EXPORT
        );

        // It annoying we have to load the order again, but the Magento API truncates the increment Id.
        $order = $this->_orderRepository->get($id);
        $websiteId = $this->_storeManager->getStore($order->getStoreId())->getWebsiteId();

        $this->_requestBuilder->setRequestBody(file_get_contents('php://input'));
        $this->_requestBuilder->setResponseBody(json_encode($response));
        $this->_requestBuilder->setScopeId($websiteId);
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
}
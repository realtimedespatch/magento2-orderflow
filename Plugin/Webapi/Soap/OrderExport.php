<?php

namespace RealtimeDespatch\OrderFlow\Plugin\Webapi\Soap;

use RealtimeDespatch\OrderFlow\Api\RequestRepositoryInterface;

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
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var RequestRepositoryInterface
     */
    protected $_requestRepository;

    /**
     * OrderExport constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface $requestBuilder
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param RequestRepositoryInterface $requestRepository
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface $requestBuilder,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        RequestRepositoryInterface $requestRepository)
    {
        $this->_objectManager = $objectManager;
        $this->_requestBuilder = $requestBuilder;
        $this->_orderRepository = $orderRepository;
        $this->_orderFactory = $orderFactory;
        $this->_storeManager = $storeManager;
        $this->_requestRepository = $requestRepository;
    }

    public function around__call(\Magento\Webapi\Controller\Soap\Request\Handler $soapServer, callable $proceed, $operation, $arguments)
    {
        $result = $proceed($operation, $arguments);

        if ($this->_isOrderExport($operation) && isset($arguments[0]->id)) {
            $request = $this->_buildOrderExportRequest($result['result'], $arguments[0]->id);
            $this->_getRequestProcessor()->process($request);
            $this->_applyExportStateToResult($result, $arguments[0]->id);
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

    /**
     * Applies the persisted export metadata to the SOAP result before it is returned.
     *
     * @param array $result
     * @param int|string $id
     *
     * @return void
     */
    protected function _applyExportStateToResult(array &$result, $id)
    {
        if ( ! isset($result['result'])) {
            return;
        }

        $order = $this->_orderFactory->create()->load($id);

        if ( ! $order->getId()) {
            return;
        }

        $this->_setOrderflowExtensionAttribute(
            $result['result'],
            'orderflowExportStatus',
            $order->getData('orderflow_export_status')
        );
        $this->_setOrderflowExtensionAttribute(
            $result['result'],
            'orderflowExportDate',
            $order->getData('orderflow_export_date')
        );
    }

    /**
     * @param mixed $result
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    protected function _setOrderflowExtensionAttribute(&$result, $key, $value)
    {
        if (is_object($result)) {
            $extensionAttributes = $this->_getResultExtensionAttributes($result);
            $setter = 'set'.ucfirst($key);

            if (is_object($extensionAttributes) && method_exists($extensionAttributes, $setter)) {
                $extensionAttributes->{$setter}($value);
                return;
            }

            if ( ! is_object($extensionAttributes)) {
                $extensionAttributes = new \stdClass();
                $result->extensionAttributes = $extensionAttributes;
            }

            $extensionAttributes->{$key} = $value;
            return;
        }

        if (is_array($result)) {
            if ( ! isset($result['extensionAttributes']) || ! is_array($result['extensionAttributes'])) {
                $result['extensionAttributes'] = [];
            }

            $result['extensionAttributes'][$key] = $value;
        }
    }

    /**
     * @param object $result
     *
     * @return mixed
     */
    protected function _getResultExtensionAttributes($result)
    {
        if (method_exists($result, 'getExtensionAttributes')) {
            return $result->getExtensionAttributes();
        }

        if (isset($result->extensionAttributes)) {
            return $result->extensionAttributes;
        }

        return null;
    }
}

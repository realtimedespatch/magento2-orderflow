<?php

namespace RealtimeDespatch\OrderFlow\Plugin\Webapi\Soap;

/**
 * Class InventoryImport
 * @package RealtimeDespatch\OrderFlow\Plugin\Webapi\Soap
 */
class InventoryImport
{
    const OP_SHIPMENT_IMPORT = 'realtimeDespatchOrderFlowInventoryRequestManagementV1Update';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \RealtimeDespatch\OrderFlow\Model\RequestFactory
     */
    protected $_requestFactory;

    /**
     * @param \Magento\Framework\Registry $registry
     * @param \RealtimeDespatch\OrderFlow\Model\RequestFactory $requestFactory
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \RealtimeDespatch\OrderFlow\Model\RequestFactory $requestFactory)
    {
        $this->_registry = $registry;
        $this->_requestFactory = $requestFactory;
    }

    /**
     * Plugin for the SOAP Request Handler.
     *
     * @param \Magento\Webapi\Controller\Soap\Request\Handler $soapServer
     * @param callable $proceed
     * @param string $operation
     * @param array $arguments
     *
     * @return mixed
     */
    public function around__call(\Magento\Webapi\Controller\Soap\Request\Handler $soapServer, callable $proceed, $operation, $arguments)
    {
        $result = $proceed($operation, $arguments);
        $requestId = $this->_registry->registry('request_id');

        if ($this->_isInventoryImport($operation) && $requestId) {
            $this->_requestFactory->create()->load($requestId)->setResponseBody(json_encode($result['result']))->save();
        }

        return $result;
    }

    /**
     * Checks whether this is a inventory import request.
     *
     * @param string $operation
     *
     * @return boolean
     */
    protected function _isInventoryImport($operation)
    {
        return $operation === self::OP_SHIPMENT_IMPORT;
    }
}
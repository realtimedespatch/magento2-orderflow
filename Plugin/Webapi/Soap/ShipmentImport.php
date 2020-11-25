<?php

namespace RealtimeDespatch\OrderFlow\Plugin\Webapi\Soap;

use Exception;
use Magento\Framework\Session\Generic;
use Magento\Webapi\Controller\Soap\Request\Handler;
use RealtimeDespatch\OrderFlow\Model\RequestFactory;

/**
 * Shipment Import SOAP API Plugin
 *
 * Captures the details of a shipment update request.
 *
 * This occurs when OrderFlow makes a call to Magento to update a shipment.
 */
class ShipmentImport
{
    const OP_SHIPMENT_IMPORT = 'realtimeDespatchOrderFlowShipmentRequestManagementV1Create';

    /**
     * @var Generic
     */
    protected $session;

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * @param Generic $session
     * @param RequestFactory $requestFactory
     */
    public function __construct(
        Generic $session,
        RequestFactory $requestFactory
    ) {
        $this->session = $session;
        $this->requestFactory = $requestFactory;
    }

    /**
     * Plugin for the SOAP Request Handler.
     *
     * @param Handler $soapServer
     * @param callable $proceed
     * @param string $operation
     * @param array $arguments
     *
     * @return mixed
     * @noinspection PhpUnusedParameterInspection
     * @throws Exception
     */
    public function around__call(
        Handler $soapServer,
        callable $proceed,
        string $operation,
        array $arguments
    ) {
        $result = $proceed($operation, $arguments);

        /* @noinspection PhpUndefinedMethodInspection */
        $requestId = $this->session->getRequestId();

        if ($this->_isShipmentImport($operation) && $requestId) {
            /** @noinspection PhpUndefinedMethodInspection */
            $request = $this->requestFactory->create()->load($requestId);
            $request->setResponseBody(json_encode($result['result']));
            $request->save();
        }

        return $result;
    }

    /**
     * Checks whether this is a shipment import request.
     *
     * @param string $operation
     *
     * @return boolean
     */
    protected function _isShipmentImport(string $operation)
    {
        return $operation === self::OP_SHIPMENT_IMPORT;
    }
}

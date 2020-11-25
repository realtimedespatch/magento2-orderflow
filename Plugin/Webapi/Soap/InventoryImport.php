<?php

namespace RealtimeDespatch\OrderFlow\Plugin\Webapi\Soap;

use Magento\Framework\Session\Generic;
use Magento\Webapi\Controller\Soap\Request\Handler;
use RealtimeDespatch\OrderFlow\Model\RequestFactory;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request as RequestResource;

/**
 * Inventory Import SOAP API Plugin
 *
 * Captures the details of an inventory update request.
 *
 * This occurs when OrderFlow makes a call to Magento to update inventory.
 */
class InventoryImport
{
    const OP_SHIPMENT_IMPORT = 'realtimeDespatchOrderFlowInventoryRequestManagementV1Update';

    /**
     * @var Generic
     */
    protected $session;

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * @var RequestResource
     */
    protected $requestResource;

    /**
     * @param Generic $session
     * @param RequestFactory $requestFactory
     * @param RequestResource $requestResource
     */
    public function __construct(
        Generic $session,
        RequestFactory $requestFactory,
        RequestResource $requestResource
    ) {
        $this->session = $session;
        $this->requestFactory = $requestFactory;
        $this->requestResource = $requestResource;
    }

    /**
     * Plugin for the SOAP Request Handler.
     *
     * @param Handler $soapServer
     * @param callable $proceed
     * @param mixed $operation
     * @param mixed $arguments
     *
     * @return mixed
     * @noinspection PhpUnusedParameterInspection
     */
    public function around__call(
        Handler $soapServer,
        callable $proceed,
        $operation,
        $arguments
    ) {
        $result = $proceed($operation, $arguments);

        /* @noinspection PhpUndefinedMethodInspection */
        $requestId = $this->session->getRequestId();

        if ($this->isInventoryImport($operation) && $requestId) {
            /** @noinspection PhpUndefinedMethodInspection */
            $request = $this->requestResource->load(
                $this->requestFactory->create(),
                $requestId
            );

            /** @noinspection PhpUndefinedMethodInspection */
            $request->setResponseBody(json_encode($result['result']))->save();
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
    protected function isInventoryImport(string $operation)
    {
        return $operation === self::OP_SHIPMENT_IMPORT;
    }
}

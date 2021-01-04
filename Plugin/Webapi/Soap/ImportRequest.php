<?php

/** @noinspection PhpCSValidationInspection */

namespace RealtimeDespatch\OrderFlow\Plugin\Webapi\Soap;

use Exception;
use Magento\Framework\Session\Generic;
use Magento\Webapi\Controller\Soap\Request\Handler;
use RealtimeDespatch\OrderFlow\Api\RequestRepositoryInterface;

/**
 * Inventory Import SOAP API Plugin
 *
 * Captures the details of an inventory update request.
 *
 * This occurs when OrderFlow makes a call to Magento to update inventory.
 */
class ImportRequest
{
    const OP_INVENTORY_IMPORT = 'realtimeDespatchOrderFlowInventoryRequestManagementV1Update';
    const OP_SHIPMENT_IMPORT = 'realtimeDespatchOrderFlowShipmentRequestManagementV1Create';

    /**
     * @var Generic
     */
    protected $session;

    /**
     * @var RequestRepositoryInterface
     */
    protected $requestRepository;

    /**
     * @param Generic $session
     * @param RequestRepositoryInterface $requestRepository
     */
    public function __construct(
        Generic $session,
        RequestRepositoryInterface $requestRepository
    ) {
        $this->session = $session;
        $this->requestRepository = $requestRepository;
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
        $reqId = $this->session->getData('request_id');

        if ( ! $reqId) {
            return $result;
        }

        if ( ! $this->isValidResponseToTrack($operation)) {
            return $result;
        }

        $response = json_encode($result['result']);
        $this->saveResponse($reqId, $response);

        return $result;
    }

    /**
     * Saves the response body to the associated request.
     *
     * @param int $reqId
     * @param string|bool $response
     * @return boolean
     */
    protected function saveResponse($reqId, $response)
    {
        try {
            $request = $this->requestRepository->get($reqId);
            $request->setResponseBody($response);
            $this->requestRepository->save($request);
        } catch (Exception $ex) {
            return false;
        }

        return true;
    }

    /**
     * Checks whether this is a valid import for us to track the response body.
     *
     * Valid:
     *
     * Inventory Update
     * Shipment Update
     *
     * @param string $operation
     *
     * @return boolean
     */
    protected function isValidResponseToTrack(string $operation)
    {
        if ($operation === self::OP_INVENTORY_IMPORT) {
            return true;
        }

        if ($operation === self::OP_SHIPMENT_IMPORT) {
            return true;
        }

        return false;
    }
}

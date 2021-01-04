<?php

/** @noinspection PhpCSValidationInspection */

namespace RealtimeDespatch\OrderFlow\Plugin\Webapi\Soap;

use Exception;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Webapi\Controller\Soap\Request\Handler;
use RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;

/**
 * Order Export SOAP API Plugin
 *
 * Captures the details of an order export request.
 *
 * This occurs when OrderFlow makes a call to Magento to retrieve the details of an order.
 */
class OrderExportRequest
{
    /* API Operation */
    const API_OPERATION = 'salesOrderRepositoryV1Get';

    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var RequestBuilderInterface
     */
    protected $requestBuilder;

    /**
     * @param RequestBuilderInterface $requestBuilder
     * @param DriverInterface $driver
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        DriverInterface $driver,
        OrderRepositoryInterface $orderRepository,
        RequestBuilderInterface $requestBuilder
    ) {
        $this->driver = $driver;
        $this->orderRepository = $orderRepository;
        $this->requestBuilder = $requestBuilder;
    }

    /**
     * Capture Order Export Request Details.
     *
     * @noinspection PhpUnusedParameterInspection
     *
     * @param Handler $soapServer
     * @param callable $proceed
     * @param $operation
     * @param $arguments
     * @return mixed
     */
    public function around__call(
        Handler $soapServer,
        callable $proceed,
        $operation,
        $arguments
    ) {
        $result = $proceed($operation, $arguments);

        // Not an order export request
        if ( ! $this->isOrderExportRequest($operation)) {
            return $result;
        }

        // No result available
        if (! isset($result['result'])) {
            return $result;
        }

        // No order identifier available
        if (! isset($arguments[0]->id)) {
            return $result;
        }

        // Build request
        $this->buildRequest($result['result'], $arguments[0]->id);

        return $result;
    }

    /**
     * Checks whether this is an order export request.
     *
     * @param string $operation
     *
     * @return boolean
     */
    protected function isOrderExportRequest(string $operation)
    {
        return $operation === self::API_OPERATION;
    }

    /**
     * Builds an export request from the order ID.
     *
     * @param array $response
     * @param string $orderId
     *
     * @return boolean
     */
    protected function buildRequest(array $response, string $orderId)
    {
        try {
            $order = $this->orderRepository->get($orderId);

            $this->requestBuilder->setRequestBody($this->driver->fileGetContents('php://input'));
            $this->requestBuilder->setResponseBody(json_encode($response));
            $this->requestBuilder->addRequestLine(json_encode(['increment_id' => $order->getIncrementId()]));

            $this->requestBuilder->saveRequest(
                RequestInterface::TYPE_EXPORT,
                RequestInterface::ENTITY_ORDER,
                RequestInterface::OP_EXPORT
            );

            return true;
        } catch (Exception $ex) {
            return false;
        }
    }
}

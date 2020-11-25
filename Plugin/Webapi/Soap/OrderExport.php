<?php

namespace RealtimeDespatch\OrderFlow\Plugin\Webapi\Soap;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Webapi\Controller\Soap\Request\Handler;
use RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface;
use RealtimeDespatch\OrderFlow\Model\Service\Request\RequestProcessor;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;

/**
 * Order Export SOAP API Plugin
 *
 * Captures the details of an order export request.
 *
 * This occurs when OrderFlow makes a call to Magento to retrieve the details of an order.
 */
class OrderExport
{
    /* API Operation */
    const API_OPERATION = 'salesOrderRepositoryV1Get';

    /**
     * @var RequestProcessor
     */
    protected $requestProcessor;

    /**
     * @var RequestBuilderInterface
     */
    protected $requestBuilder;

    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @param RequestProcessor $requestProcessor
     * @param RequestBuilderInterface $requestBuilder
     * @param DriverInterface $driver
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        RequestProcessor $requestProcessor,
        RequestBuilderInterface $requestBuilder,
        DriverInterface $driver,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->requestProcessor = $requestProcessor;
        $this->requestBuilder = $requestBuilder;
        $this->driver = $driver;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Capture Order Export Request Details.
     *
     * @param Handler $soapServer
     * @param callable $proceed
     * @param $operation
     * @param $arguments
     * @return mixed
     * @throws FileSystemException
     * @noinspection PhpUnusedParameterInspection
     */
    public function around__call(
        Handler $soapServer,
        callable $proceed,
        $operation,
        $arguments
    ) {
        $result = $proceed($operation, $arguments);

        if ($this->isOrderExportRequest($operation) && isset($result['result']) && isset($arguments[0]->id)) {
            $request = $this->buildOrderExportRequest($result['result'], $arguments[0]->id);
            $this->requestProcessor->process($request);
        }

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
     * @return RequestInterface
     * @throws FileSystemException
     */
    protected function buildOrderExportRequest(array $response, string $orderId)
    {
        $this->requestBuilder->setRequestData(
            RequestInterface::TYPE_EXPORT,
            RequestInterface::ENTITY_ORDER,
            RequestInterface::OP_EXPORT
        );

        $order = $this->orderRepository->get($orderId);

        $this->requestBuilder->setRequestBody($this->driver->fileGetContents('php://input'));
        $this->requestBuilder->setResponseBody(json_encode($response));
        $this->requestBuilder->addRequestLine(json_encode(['increment_id' => $order->getIncrementId()]));

        return $this->requestBuilder->saveRequest();
    }
}

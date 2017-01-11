<?php

namespace RealtimeDespatch\OrderFlow\Model\Service;

use RealtimeDespatch\OrderFlow\Api\OrderRequestManagementInterface;

/**
 * Class OrderRequestService
 *
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OrderRequestService implements OrderRequestManagementInterface
{
    /**
     * @var \RealtimeDespatch\OrderFlow\Model\Builder\RequestBuilder
     */
    protected $requestBuilder;

    /**
     * Constructor
     *
     * @param \RealtimeDespatch\OrderFlow\Model\Builder\RequestBuilder $requestBuilder
     */
    public function __construct(
        \RealtimeDespatch\OrderFlow\Model\Builder\RequestBuilder $requestBuilder) {
        $this->requestBuilder = $requestBuilder;
    }

    /**
     * Marks an order as exported.
     *
     * @api
     * @param string $reference
     *
     * @return mixed
     */
    public function export($reference)
    {
        $date = new \DateTime;
        $received = $date->format("Y-m-d H:i:s");

        // Build the request.
        $this->requestBuilder->setRequestData(
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::TYPE_EXPORT,
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::ENTITY_ORDER,
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::OP_EXPORT
        );

        $this->requestBuilder->addRequestLine(json_encode(array('increment_id' => $reference)));
        $this->requestBuilder->saveRequest();

        return str_replace(' ','T', $received);
    }
}
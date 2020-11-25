<?php

namespace RealtimeDespatch\OrderFlow\Model\Service;

use DateTime;
use Magento\Framework\Exception\CouldNotSaveException;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Api\ProductRequestManagementInterface;
use RealtimeDespatch\OrderFlow\Model\Builder\RequestBuilder;

/**
 * Class ProductRequestService
 *
 * Service Class for Processing Product Requests.
 *
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductRequestService implements ProductRequestManagementInterface
{
    /**
     * @var RequestBuilder
     */
    protected $requestBuilder;

    /**
     * Constructor
     *
     * @param RequestBuilder $requestBuilder
     */
    public function __construct(
        RequestBuilder $requestBuilder
    ) {
        $this->requestBuilder = $requestBuilder;
    }

    /**
     * Marks a product as exported.
     *
     * @param string $reference
     *
     * @return mixed
     * @throws CouldNotSaveException
     * @api
     */
    public function export(string $reference)
    {
        $date = new DateTime;
        $received = $date->format("Y-m-d H:i:s");

        // Build the request.
        $this->requestBuilder->setRequestData(
            RequestInterface::TYPE_EXPORT,
            RequestInterface::ENTITY_PRODUCT,
            RequestInterface::OP_EXPORT
        );

        $this->requestBuilder->addRequestLine(json_encode(['sku' => $reference]));
        $this->requestBuilder->saveRequest();

        return str_replace(
            ' ',
            'T',
            $received
        );
    }
}

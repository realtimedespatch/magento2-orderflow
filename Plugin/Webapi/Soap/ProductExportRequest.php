<?php

/** @noinspection PhpCSValidationInspection */

namespace RealtimeDespatch\OrderFlow\Plugin\Webapi\Soap;

use Exception;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Webapi\Controller\Soap\Request\Handler;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface;
use RealtimeDespatch\OrderFlow\Model\Service\Request\RequestProcessor;

/**
 * Product Export SOAP API Plugin
 *
 * Captures the details of a product export request.
 *
 * This occurs when OrderFlow makes a call to Magento to retrieve the details of a product.
 */
class ProductExportRequest
{
    /* API Operation */
    const API_OPERATION = 'catalogProductRepositoryV1Get';

    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @var RequestBuilderInterface
     */
    protected $requestBuilder;

    /**
     * @param RequestBuilderInterface $requestBuilder
     * @param DriverInterface $driver
     */
    public function __construct(
        DriverInterface $driver,
        RequestBuilderInterface $requestBuilder
    ) {
        $this->driver = $driver;
        $this->requestBuilder = $requestBuilder;
    }

    /**
     * Capture Product Export Request Details.
     *
     * @param Handler $soapServer
     * @param callable $proceed
     * @param $operation
     * @param $arguments
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

        // Not an order export request
        if ( ! $this->isProductExportRequest($operation)) {
            return $result;
        }

        // No result available
        if (! isset($result['result'])) {
            return $result;
        }

        // No sku available
        if (! isset($arguments[0]->sku)) {
            return $result;
        }

        // Build request
        $this->buildRequest($result['result'], $arguments[0]->sku);

        return $result;
    }

    /**
     * Checks whether this is a product export request.
     *
     * @param string $operation
     *
     * @return boolean
     */
    protected function isProductExportRequest(string $operation)
    {
        return $operation === self::API_OPERATION;
    }

    /**
     * Build Request.
     *
     * @param array $response
     * @param string $sku
     *
     * @return boolean
     */
    protected function buildRequest(array $response, string $sku)
    {
        try {
            $this->requestBuilder->setRequestBody($this->driver->fileGetContents('php://input'));
            $this->requestBuilder->setResponseBody(json_encode($response));
            $this->requestBuilder->addRequestLine(json_encode(['sku' => $sku]));

            $this->requestBuilder->saveRequest(
                RequestInterface::TYPE_EXPORT,
                RequestInterface::ENTITY_PRODUCT,
                RequestInterface::OP_EXPORT
            );

            return true;
        } catch (Exception $ex) {
            return false;
        }
    }
}

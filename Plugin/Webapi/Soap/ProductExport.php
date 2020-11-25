<?php

namespace RealtimeDespatch\OrderFlow\Plugin\Webapi\Soap;

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
class ProductExport
{
    /* API Operation */
    const API_OPERATION = 'catalogProductRepositoryV1Get';

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
     * @param RequestProcessor $requestProcessor
     * @param RequestBuilderInterface $requestBuilder
     * @param DriverInterface $driver
     */
    public function __construct(
        RequestProcessor $requestProcessor,
        RequestBuilderInterface $requestBuilder,
        DriverInterface $driver
    ) {
        $this->requestProcessor = $requestProcessor;
        $this->requestBuilder = $requestBuilder;
        $this->driver = $driver;
    }

    /**
     * Capture Product Export Request Details.
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

        if ($this->isProductExportRequest($operation) && isset($result['result']) && isset($arguments[0]->sku)) {
            $request = $this->buildProductExportRequest($result['result'], $arguments[0]->sku);
            $this->requestProcessor->process($request);
        }

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
     * Build Product Export Request.
     *
     * @param array $response
     * @param string $sku
     *
     * @return RequestInterface
     * @throws FileSystemException
     */
    protected function buildProductExportRequest(array $response, string $sku)
    {
        $this->requestBuilder->setRequestData(
            RequestInterface::TYPE_EXPORT,
            RequestInterface::ENTITY_PRODUCT,
            RequestInterface::OP_EXPORT
        );

        $this->requestBuilder->setRequestBody($this->driver->fileGetContents('php://input'));
        $this->requestBuilder->setResponseBody(json_encode($response));
        $this->requestBuilder->addRequestLine(json_encode(['sku' => $sku]));

        return $this->requestBuilder->saveRequest();
    }
}

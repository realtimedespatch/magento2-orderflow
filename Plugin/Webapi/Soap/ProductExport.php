<?php

namespace RealtimeDespatch\OrderFlow\Plugin\Webapi\Soap;

class ProductExport
{
    const OP_PRODUCT_EXPORT = 'catalogProductRepositoryV1Get';

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface
     */
    protected $_requestBuilder;

    /**
     * @var \RealtimeDespatch\OrderFlow\Helper\Export\Product
     */
    protected $_helper;

    /**
     * ProductExport constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface $requestBuilder
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface $requestBuilder,
        \RealtimeDespatch\OrderFlow\Helper\Export\Product $helper
    )
    {
        $this->_objectManager = $objectManager;
        $this->_requestBuilder = $requestBuilder;
        $this->_helper = $helper;
    }

    public function around__call(\Magento\Webapi\Controller\Soap\Request\Handler $soapServer, callable $proceed, $operation, $arguments)
    {
        $result = $proceed($operation, $arguments);

        if ($this->_isProductExport($operation) && isset($arguments[0]->sku)) {
            $sku = $arguments[0]->sku;
            $this->_getRequestProcessor()->process($this->_buildProductExportRequest($result['result'], $sku));
            if (!$this->_helper->isProductExportEnabledForProductWebsites($sku)) {
                throw new \Magento\Framework\Webapi\Exception(
                    __("Product '{$sku}' is not in any product export enabled websites"),
                    0,
                    \Magento\Framework\Webapi\Exception::HTTP_INTERNAL_ERROR
                );
            }
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
    protected function _isProductExport($operation)
    {
        return $operation === self::OP_PRODUCT_EXPORT;
    }

    /**
     * Builds an export request from the product SKU.
     *
     * @param string $response
     * @param string $sku
     *
     * @return \RealtimeDespatch\OrderFlow\Model\Request
     */
    protected function _buildProductExportRequest($response, $sku)
    {
        $this->_requestBuilder->setRequestData(
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::TYPE_EXPORT,
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::ENTITY_PRODUCT,
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::OP_EXPORT
        );

        $this->_requestBuilder->setRequestBody(file_get_contents('php://input'));
        $this->_requestBuilder->setResponseBody(json_encode($response));
        $this->_requestBuilder->addRequestLine(json_encode(array('sku' => $sku)));

        return $this->_requestBuilder->saveRequest();
    }

    /**
     * Retrieve the request processor instance.
     *
     * @return RealtimeDespatch\OrderFlow\Model\Service\Request\RequestProcessor
     */
    protected function _getRequestProcessor()
    {
        return $this->_objectManager->create('ProductExportRequestProcessor');
    }
}

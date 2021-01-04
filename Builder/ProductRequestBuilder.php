<?php

namespace RealtimeDespatch\OrderFlow\Builder;

use Exception;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Serialize\Serializer\Json;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Api\ExportRequestBuilderInterface;
use RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface;
use RealtimeDespatch\OrderFlow\Helper\Export\Product;

class ProductRequestBuilder implements ExportRequestBuilderInterface
{
    /**
     * @var Product
     */
    protected $helper;

    /**
     * @var RequestBuilderInterface
     */
    protected $requestBuilder;

    /**
     * @var Json
     */
    protected $json;

    /**
     * @param Product $helper
     * @param RequestBuilderInterface $requestBuilder
     * @param Json $json
     */
    public function __construct(
        Product $helper,
        RequestBuilderInterface $requestBuilder,
        Json $json
    ) {
        $this->helper = $helper;
        $this->requestBuilder = $requestBuilder;
        $this->json = $json;
    }

    /**
     * Builds new requests for products that are available for creation and update.
     *
     * @param int $websiteId
     */
    public function build(int $websiteId)
    {
        $this->buildCreateProductRequests($websiteId);
        $this->buildUpdateProductRequests($websiteId);
    }

    /**
     * Builds create product requests.
     *
     * @param int $websiteId
     * @return false|void
     */
    protected function buildCreateProductRequests($websiteId)
    {
        try {
            $products = $this->helper->getCreateableProducts($websiteId);

            if (count($products) === 0) {
                return;
            }

            $this->requestBuilder->setScopeId($websiteId);

            foreach ($products as $product) {
                $this->requestBuilder->addRequestLine(json_encode(['sku' => $product->getSku()]));
            }

            $this->requestBuilder->saveRequest(
                RequestInterface::TYPE_EXPORT,
                RequestInterface::ENTITY_PRODUCT,
                RequestInterface::OP_CREATE
            );
        } catch (Exception $ex) {
            return false;
        }
    }

    /**
     * Builds update product requests.
     *
     * @param int $websiteId
     * @return false|void
     */
    protected function buildUpdateProductRequests($websiteId)
    {
        try {
            $products = $this->helper->getUpdateableProducts($websiteId);

            if (count($products) === 0) {
                return;
            }

            $this->requestBuilder->setScopeId($websiteId);

            foreach ($products as $product) {
                $this->requestBuilder->addRequestLine($this->getRequestLine($product));
            }

            $this->requestBuilder->saveRequest(
                RequestInterface::TYPE_EXPORT,
                RequestInterface::ENTITY_PRODUCT,
                RequestInterface::OP_UPDATE
            );
        } catch (Exception $ex) {
            return false;
        }
    }

    /**
     * Request Line Getter.
     *
     * @param ProductInterface $product
     * @return bool|string
     */
    protected function getRequestLine(ProductInterface $product)
    {
        return $this->json->serialize(['sku' => $product->getSku()]);
    }
}

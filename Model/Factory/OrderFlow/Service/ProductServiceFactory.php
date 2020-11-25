<?php

namespace RealtimeDespatch\OrderFlow\Model\Factory\OrderFlow\Service;

use RealtimeDespatch\OrderFlow\Helper\Api;
use SixBySix\RealtimeDespatch\Gateway\Factory\DefaultGatewayFactory;
use SixBySix\RealtimeDespatch\Service\ProductService;

/**
 * Product Service Factory.
 *
 * OrderFlow Product Service Factory.
 */
class ProductServiceFactory
{
    /**
     * API Helper.
     *
     * @param Api
     */
    protected $helper;

    /**
     * @param Api $helper
     */
    public function __construct(Api $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Returns the OrderFlow Product Service.
     *
     * @param integer|null $scopeId
     *
     * @return ProductService
     */
    public function getService($scopeId = null)
    {
        $credentials = $this->helper->getCredentials($scopeId);
        $factory = new DefaultGatewayFactory();

        return new ProductService($factory->create($credentials));
    }
}

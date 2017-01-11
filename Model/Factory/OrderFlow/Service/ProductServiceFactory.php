<?php

namespace RealtimeDespatch\OrderFlow\Model\Factory\OrderFlow\Service;

use \SixBySix\RealtimeDespatch\Gateway\Factory\DefaultGatewayFactory;
use \SixBySix\RealtimeDespatch\Service\ProductService;

/**
 * Class ProductService
 * @package RealtimeDespatch\OrderFlow\Model\Factory\OrderFlow\Service
 */
class ProductServiceFactory
{
    /**
     * API Helper.
     *
     * @param RealtimeDespatch\OrderFlow\Helper\Api
     */
    protected $_helper;

    /**
     * @param \RealtimeDespatch\OrderFlow\Helper\Api $helper
     */
    public function __construct(
        \RealtimeDespatch\OrderFlow\Helper\Api $helper
    ) {
        $this->_helper = $helper;
    }

    /**
     * Returns the OrderFlow Product Service.
     *
     * @param integer|null $scopeId
     *
     * @return \RealtimeDespatch\OrderFlow\Model\Factory\OrderFlow\Service\ProductService
     */
    public function getService($scopeId = null)
    {
        $credentials = $this->_helper->getCredentials($scopeId);
        $factory = new DefaultGatewayFactory();
        $service = new ProductService($factory->create($credentials));

        return $service;
    }
}
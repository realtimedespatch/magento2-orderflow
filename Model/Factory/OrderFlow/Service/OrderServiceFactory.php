<?php

namespace RealtimeDespatch\OrderFlow\Model\Factory\OrderFlow\Service;

use RealtimeDespatch\OrderFlow\Helper\Api;
use SixBySix\RealtimeDespatch\Gateway\Factory\DefaultGatewayFactory;
use SixBySix\RealtimeDespatch\Service\OrderService;

/**
 * Order Service Factory.
 *
 * OrderFlow Order Service Factory.
 */
class OrderServiceFactory
{
    /**
     * API Helper.
     *
     * @param Api
     */
    protected $helper;

    /**
     * @var DefaultGatewayFactory
     */
    protected $gatewayFactory;

    /**
     * @param Api $helper
     * @param DefaultGatewayFactory $gatewayFactory
     */
    public function __construct(
        Api $helper,
        DefaultGatewayFactory $gatewayFactory
    ) {
        $this->helper = $helper;
        $this->gatewayFactory = $gatewayFactory;
    }

    /**
     * Returns the OrderFlow Product Service.
     *
     * @param integer|null $scopeId
     *
     * @return OrderService
     */
    public function getService($scopeId = null)
    {
        $credentials = $this->helper->getCredentials($scopeId);
        $gateway = $this->gatewayFactory->create($credentials);

        return new OrderService($gateway);
    }
}

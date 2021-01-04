<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Factory\OrderFlow\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Helper\Api;
use RealtimeDespatch\OrderFlow\Model\Factory\OrderFlow\Service\ProductServiceFactory;
use SixBySix\RealtimeDespatch\Api\Credentials;
use SixBySix\RealtimeDespatch\Gateway\DefaultGateway;
use SixBySix\RealtimeDespatch\Gateway\Factory\DefaultGatewayFactory;

class ProductServiceFactoryTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $helper;

    /**
     * @var MockObject
     */
    protected $gatewayFactory;

    /**
     * @var ProductServiceFactory
     */
    protected $productServiceFactory;

    public function setUp()
    {
        $this->helper = $this->getMockBuilder(Api::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->gatewayFactory = $this->getMockBuilder(DefaultGatewayFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->productServiceFactory = new ProductServiceFactory(
            $this->helper,
            $this->gatewayFactory
        );
    }

    public function testGetService()
    {
        $scopeId = 1;

        $credentials = $this->getMockBuilder(Credentials::class)
            ->getMock();

        $this->helper->expects($this->once())
            ->method('getCredentials')
            ->with($scopeId)
            ->willReturn($credentials);

        $gateway = $this->getMockBuilder(DefaultGateway::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->gatewayFactory->expects($this->once())
            ->method('create')
            ->with($credentials)
            ->willReturn($gateway);

        $this->assertInstanceOf(
            'SixBySix\RealtimeDespatch\Service\ProductService',
            $this->productServiceFactory->getService($scopeId)
        );
    }
}

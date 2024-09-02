<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Factory\OrderFlow\Service;

use RealtimeDespatch\OrderFlow\Helper\Api;
use RealtimeDespatch\OrderFlow\Model\Factory\OrderFlow\Service\OrderServiceFactory;
use SixBySix\RealtimeDespatch\Api\Credentials;
use SixBySix\RealtimeDespatch\Service\OrderService;

class OrderServiceFactoryTest extends \PHPUnit\Framework\TestCase
{
    protected OrderServiceFactory $orderServiceFactory;
    protected Api $mockApiHelper;
    protected Credentials $mockCredentials;

    protected function setUp(): void
    {
        $this->mockApiHelper = $this->createMock(Api::class);
        $this->mockCredentials = $this->createMock(Credentials::class);

        $this->orderServiceFactory = new OrderServiceFactory(
            $this->mockApiHelper
        );
    }

    public function testGetService(): void
    {
        $this->mockApiHelper
            ->expects($this->once())
            ->method('getCredentials')
            ->willReturn($this->mockCredentials);

        $this->mockCredentials
            ->expects($this->once())
            ->method('getUsername')
            ->willReturn('test_username');

        $this->mockCredentials
            ->expects($this->once())
            ->method('getPassword')
            ->willReturn('test_password');

        $this->mockCredentials
            ->expects($this->once())
            ->method('getEndpoint')
            ->willReturn('https://example.com');

        $result = $this->orderServiceFactory->getService();
        $this->assertInstanceOf(OrderService::class, $result);
    }
}
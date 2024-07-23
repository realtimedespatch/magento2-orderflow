<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Service\Export\Type;

use SixBySix\RealtimeDespatch\Service\OrderService;
use RealtimeDespatch\OrderFlow\Model\Factory\OrderFlow\Service\OrderServiceFactory;
use RealtimeDespatch\OrderFlow\Model\Service\Export\Type\OrderCancelExporterType;

class OrderCancelExporterTypeTest extends AbstractExporterTypeTest
{
    protected OrderServiceFactory $mockOrderServiceFactory;
    protected OrderService $mockOrderService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockOrderServiceFactory = $this->createMock(OrderServiceFactory::class);
        $this->mockOrderService = $this->createMock(OrderService::class);

        $this->mockOrderServiceFactory
            ->method('getService')
            ->willReturn($this->mockOrderService);

        $this->exporterType = new OrderCancelExporterType(
            $this->mockScopeConfig,
            $this->mockLogger,
            $this->mockObjectManager,
            $this->mockOrderServiceFactory
        );
    }

    protected function getTestExportRequestLineBody(): object
    {
        return (object) [
            'increment_id' => '100000001',
        ];
    }

    protected function getEnabledConfigPath() : string
    {
        return 'orderflow_order_export/settings/is_enabled';
    }

    protected function getTypeName() : string
    {
        return 'Order';
    }
}
<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Service\Export\Type;

use RealtimeDespatch\OrderFlow\Model\Factory\OrderFlow\Service\OrderServiceFactory;
use RealtimeDespatch\OrderFlow\Model\Service\Export\Type\OrderCancelExporterType;
use RealtimeDespatch\OrderFlow\Model\Service\Export\Type\OrderCreateExporterType;
use SixBySix\RealtimeDespatch\Service\OrderService;

class OrderCreateExporterTypeTest extends AbstractExporterTypeTest
{
    protected OrderServiceFactory $mockOrderServiceFactory;
    protected OrderService $mockOrderService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockOrderServiceFactory = $this->createMock(OrderServiceFactory::class);
        $this->mockOrderService = $this->createMock(OrderService::class);
        $this->mockOrderServiceFactory->method('getService')->willReturn($this->mockOrderService);

        $this->exporterType = new OrderCreateExporterType(
            $this->mockScopeConfig,
            $this->mockLogger,
            $this->mockObjectManager,
            $this->mockOrderServiceFactory
        );
    }

    protected function getTestExportRequestLineBody(): object
    {
        return (object) [
            'entity_id' => 1,
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
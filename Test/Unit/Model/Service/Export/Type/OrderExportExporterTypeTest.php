<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Service\Export\Type;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderRepository;
use RealtimeDespatch\OrderFlow\Model\Factory\OrderFlow\Service\OrderServiceFactory;
use RealtimeDespatch\OrderFlow\Model\Service\Export\Type\OrderCancelExporterType;
use RealtimeDespatch\OrderFlow\Model\Service\Export\Type\OrderCreateExporterType;
use RealtimeDespatch\OrderFlow\Model\Service\Export\Type\OrderExportExporterType;

class OrderExportExporterTypeTest extends AbstractExporterTypeTest
{
    protected Order $mockOrderRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockOrderRepository = $this->createMock(Order::class);

        $this->mockOrderRepository
            ->method('loadByIncrementId')
            ->willReturnSelf();


        $this->exporterType = new OrderExportExporterType(
            $this->mockScopeConfig,
            $this->mockLogger,
            $this->mockObjectManager,
            $this->mockOrderRepository
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
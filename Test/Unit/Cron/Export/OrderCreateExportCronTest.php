<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Cron\Export;

use Magento\Framework\Model\AbstractModel;
use Magento\Sales\Model\Order;
use RealtimeDespatch\OrderFlow\Cron\Export\OrderCreateExport;
use RealtimeDespatch\OrderFlow\Helper\Export\Order as ExportOrderHelper;

class OrderCreateExportCronTest extends AbstractExportCronTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->mockExportHelper = $this->createMock(ExportOrderHelper::class);

        $this->exportCron = new OrderCreateExport(
            $this->mockExportHelper,
            $this->mockLogger,
            $this->mockRequestBuilder,
            $this->mockObjectManager,
            $this->mockWebsiteFactory
        );
    }

    /**
     * @dataProvider testExecuteDataProvider
     * @return void
     */
    public function testExecute(bool $isEnabled, int $numEntities = 1): void
    {
        $this->mockExportHelper
            ->method('isEnabled')
            ->with(1)
            ->willReturn($isEnabled);

        $this->mockExportHelper
            ->expects(($isEnabled) ? $this->once() : $this->never())
            ->method('getCreateableOrders')
            ->with($this->mockWebsite)
            ->willReturn(
                $this->getExportableEntities($numEntities)
            );

        if ($isEnabled && $numEntities > 0) {
            $this->mockRequestBuilder
                ->expects($this->exactly($numEntities))
                ->method('setRequestData')
                ->with(
                    'Export',
                    'Order',
                    'Create'
                );

            $this->mockRequestBuilder
                ->expects($this->exactly($numEntities))
                ->method('addRequestLine')
                ->with(json_encode([
                    'entity_id' => 1,
                    'increment_id' => '100000001'
                ]));

            $this->mockRequest
                ->expects($this->once())
                ->method('getEntity')
                ->willReturn('Order');

            $this->mockRequest
                ->expects($this->once())
                ->method('getOperation')
                ->willReturn('Create');

            $this->mockObjectManager
                ->expects($this->once())
                ->method('create')
                ->with('OrderCreateRequestProcessor')
                ->willReturn($this->mockRequestProcessor);
        }

        parent::testExecute($isEnabled, $numEntities);
    }

    protected function getMockEntity(): AbstractModel
    {
        $mockOrder = $this->createMock(Order::class);

        $mockOrder
            ->method('getEntityId')
            ->willReturn(1);

        $mockOrder
            ->method('getIncrementId')
            ->willReturn('100000001');

        return $mockOrder;
    }
}
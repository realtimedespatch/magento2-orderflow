<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Observer;

use Magento\Framework\DB\Transaction;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use RealtimeDespatch\OrderFlow\Model\Export;
use RealtimeDespatch\OrderFlow\Model\ExportLine;
use RealtimeDespatch\OrderFlow\Observer\OrderExport;

class OrderExportTest extends \PHPUnit\Framework\TestCase
{
    protected OrderExport $orderExport;
    protected Transaction $mockTxn;
    protected OrderInterface $mockOrder;
    protected Observer $mockObserver;
    protected Export $mockExport;

    protected function setUp(): void
    {
        $this->mockTxn = $this->createMock(Transaction::class);
        $this->mockOrder = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['loadByIncrementId', 'getId'])
            ->addMethods(['setOrderflowExportStatus'])
            ->getMock();
        $this->mockObserver = $this->createMock(Observer::class);
        $this->mockExport = $this->createMock(Export::class);

        $this->orderExport = new OrderExport(
            $this->mockTxn,
            $this->mockOrder
        );
    }

    public function testExecuteIgnore(): void
    {
        $this->mockObserver
            ->expects($this->once())
            ->method('getData')
            ->with('export')
            ->willReturn($this->mockExport);

        $this->mockExport
            ->expects($this->once())
            ->method('isOrderExport')
            ->willReturn(false);

        $this->mockExport
            ->expects($this->never())
            ->method('getLines');

        $this->mockTxn
            ->expects($this->never())
            ->method('save');

        $this->orderExport->execute($this->mockObserver);
    }

    public function testExecute(): void
    {
        $this->mockObserver
            ->expects($this->once())
            ->method('getData')
            ->with('export')
            ->willReturn($this->mockExport);

        $this->mockExport
            ->expects($this->once())
            ->method('isOrderExport')
            ->willReturn(true);

        $this->mockExport
            ->expects($this->once())
            ->method('getLines')
            ->willReturn($this->getMockExportLines());

        $this->mockOrder
            ->expects($this->once())
            ->method('loadByIncrementId')
            ->with('100000001')
            ->willReturnSelf();

        $this->mockOrder
            ->method('getId')
            ->willReturn(1);

        $this->mockOrder
            ->expects($this->once())
            ->method('setOrderflowExportStatus')
            ->with('exported', true, \Magento\Store\Model\Store::DEFAULT_STORE_ID);

        $this->mockTxn
            ->expects($this->once())
            ->method('save');

        $this->orderExport->execute($this->mockObserver);
    }

    public function testExecuteOrderNotFound(): void
    {
        $this->mockObserver
            ->expects($this->once())
            ->method('getData')
            ->with('export')
            ->willReturn($this->mockExport);

        $this->mockExport
            ->expects($this->once())
            ->method('isOrderExport')
            ->willReturn(true);

        $this->mockExport
            ->expects($this->once())
            ->method('getLines')
            ->willReturn($this->getMockExportLines());

        $this->mockOrder
            ->expects($this->once())
            ->method('loadByIncrementId')
            ->with('100000001')
            ->willThrowException(new NoSuchEntityException());

        $this->mockOrder
            ->method('getId')
            ->willReturn(null);

        $this->mockOrder
            ->expects($this->never())
            ->method('setOrderflowExportStatus');

        $this->mockTxn
            ->expects($this->once())
            ->method('save');

        $this->orderExport->execute($this->mockObserver);
    }

    protected function getMockExportLines(): array
    {
        $mockExportLine1 = $this->createMock(ExportLine::class);
        $mockExportLine1
            ->expects($this->once())
            ->method('isFailure')
            ->willReturn(true);
        $mockExportLine1
            ->expects($this->once())
            ->method('isCancellation')
            ->willReturn(true);
        $mockExportLine1
            ->expects($this->never())
            ->method('getReference');

        $mockExportLine2 = $this->createMock(ExportLine::class);
        $mockExportLine2
            ->expects($this->once())
            ->method('isFailure')
            ->willReturn(false);

        $mockExportLine2
            ->expects($this->never())
            ->method('isCancellation');

        $mockExportLine2
            ->method('getReference')
            ->willReturn('100000001');

        $mockExportLine2
            ->method('getEntityExportStatus')
            ->willReturn('exported');

        return [
            $mockExportLine1,
            $mockExportLine2,
        ];
    }

    public function testExecuteNoOrderId(): void
    {
        $this->mockObserver
            ->expects($this->once())
            ->method('getData')
            ->with('export')
            ->willReturn($this->mockExport);

        $this->mockExport
            ->expects($this->once())
            ->method('isOrderExport')
            ->willReturn(true);

        $this->mockExport
            ->expects($this->once())
            ->method('getLines')
            ->willReturn($this->getMockExportLines());

        $this->mockOrder
            ->expects($this->once())
            ->method('loadByIncrementId')
            ->with('100000001')
            ->willReturnSelf();

        $this->mockOrder
            ->method('getId')
            ->willReturn(null);

        $this->mockOrder
            ->expects($this->never())
            ->method('setOrderflowExportStatus');

        $this->mockTxn
            ->expects($this->never())
            ->method('addObject');

        $this->orderExport->execute($this->mockObserver);
    }
}
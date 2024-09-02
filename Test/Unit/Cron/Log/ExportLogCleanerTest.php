<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Cron\Log;

use RealtimeDespatch\OrderFlow\Cron\Log\ExportLogCleaner;
use \RealtimeDespatch\OrderFlow\Helper\Log\Cleaning as CleaningHelper;
use RealtimeDespatch\OrderFlow\Model\Export;
use RealtimeDespatch\OrderFlow\Model\ExportFactory;
use Psr\Log\LoggerInterface;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Export\Collection;


class ExportLogCleanerTest extends \PHPUnit\Framework\TestCase
{
    protected ExportLogCleaner $exportLogCleaner;
    protected LoggerInterface $mockLogger;
    protected ExportFactory $mockExportFactory;
    protected Export $mockExport;
    protected Collection $mockCollection;
    protected CleaningHelper $mockHelper;

    protected function setUp(): void
    {
        $this->mockLogger = $this->createMock(LoggerInterface::class);
        $this->mockExportFactory = $this->createMock(ExportFactory::class);
        $this->mockExport = $this->createMock(Export::class);
        $this->mockHelper = $this->createMock(CleaningHelper::class);
        $this->mockCollection = $this->createMock(Collection::class);

        $this->exportLogCleaner = new ExportLogCleaner(
            $this->mockLogger,
            $this->mockExportFactory,
            $this->mockHelper
        );
    }

    public function testExecuteDisable(): void
    {
        $this->mockHelper
            ->method('isEnabled')
            ->willReturn(false);

        $this->mockExportFactory
            ->expects($this->never())
            ->method('create');

        $this->exportLogCleaner->execute();
    }

    public function testExecute(): void
    {
        $this->mockHelper
            ->method('isEnabled')
            ->willReturn(true);

        $this->mockExportFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->mockExport);

        $this->mockExport
            ->expects($this->once())
            ->method('getCollection')
            ->willReturn($this->mockCollection);

        $logDuration = rand(7, 365);
        $expectedDate = date('Y-m-d', strtotime("-". ($logDuration - 1) . " days"));

        $this->mockHelper
            ->method('getExportLogDuration')
            ->willReturn($logDuration);

        $this->mockCollection
            ->expects($this->once())
            ->method('addFieldToFilter')
            ->with('created_at', ['lteq' => $expectedDate])
            ->willReturnSelf();

        $this->mockCollection
            ->expects($this->once())
            ->method('walk')
            ->with('delete');

        $this->exportLogCleaner->execute();

    }
}
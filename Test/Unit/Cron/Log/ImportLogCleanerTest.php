<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Cron\Log;

use RealtimeDespatch\OrderFlow\Cron\Log\ImportLogCleaner;
use \RealtimeDespatch\OrderFlow\Helper\Log\Cleaning as CleaningHelper;
use RealtimeDespatch\OrderFlow\Model\Import;
use RealtimeDespatch\OrderFlow\Model\ImportFactory;
use Psr\Log\LoggerInterface;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Import\Collection;


class ImportLogCleanerTest extends \PHPUnit\Framework\TestCase
{
    protected ImportLogCleaner $importLogCleaner;
    protected LoggerInterface $mockLogger;
    protected ImportFactory $mockImportFactory;
    protected Import $mockImport;
    protected Collection $mockCollection;
    protected CleaningHelper $mockHelper;

    protected function setUp(): void
    {
        $this->mockLogger = $this->createMock(LoggerInterface::class);
        $this->mockImportFactory = $this->createMock(ImportFactory::class);
        $this->mockImport = $this->createMock(Import::class);
        $this->mockHelper = $this->createMock(CleaningHelper::class);
        $this->mockCollection = $this->createMock(Collection::class);

        $this->importLogCleaner = new ImportLogCleaner(
            $this->mockLogger,
            $this->mockImportFactory,
            $this->mockHelper
        );
    }

    public function testExecuteDisable(): void
    {
        $this->mockHelper
            ->method('isEnabled')
            ->willReturn(false);

        $this->mockImportFactory
            ->expects($this->never())
            ->method('create');

        $this->importLogCleaner->execute();
    }

    public function testExecute(): void
    {
        $this->mockHelper
            ->method('isEnabled')
            ->willReturn(true);

        $this->mockImportFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->mockImport);

        $this->mockImport
            ->expects($this->once())
            ->method('getCollection')
            ->willReturn($this->mockCollection);

        $logDuration = rand(7, 365);
        $expectedDate = date('Y-m-d', strtotime("-". ($logDuration - 1) . " days"));

        $this->mockHelper
            ->method('getImportLogDuration')
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

        $this->importLogCleaner->execute();
    }
}
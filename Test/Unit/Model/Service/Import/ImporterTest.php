<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Service\Import;

use Magento\Framework\Event\ManagerInterface;
use RealtimeDespatch\OrderFlow\Api\ImporterTypeInterface;
use RealtimeDespatch\OrderFlow\Model\Service\Import\Importer;

class ImporterTest extends \PHPUnit\Framework\TestCase
{
    protected ImporterTypeInterface $mockImporterType;
    protected ManagerInterface $mockEventManager;
    protected Importer $importer;

    protected function setUp(): void
    {
        $this->mockImporterType = $this->createMock(ImporterTypeInterface::class);
        $this->mockEventManager = $this->createMock(ManagerInterface::class);
        $this->importer = new Importer($this->mockImporterType, $this->mockEventManager);
    }

    public function testImportDisabled(): void
    {
        $this->mockImporterType
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(false);

        $this->mockImporterType
            ->expects($this->never())
            ->method('import');

        $this->importer->import($this->createMock(\RealtimeDespatch\OrderFlow\Model\Request::class));
    }

    public function testImportException(): void
    {
        $this->mockImporterType
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->mockImporterType
            ->expects($this->once())
            ->method('getType')
            ->willReturn('ImportType');

        $exception = new \Exception('Test Exception');

        $this->mockImporterType
            ->expects($this->once())
            ->method('import')
            ->willThrowException($exception);

        $this->mockEventManager
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                'orderflow_exception',
                ['exception' => $exception, 'type' => 'ImportType', 'process' => 'import']
            );

        $mockRequest = $this->createMock(\RealtimeDespatch\OrderFlow\Model\Request::class);

        $this->importer->import($mockRequest);
    }

    public function testImport(): void
    {
        $this->mockImporterType
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->mockImporterType
            ->expects($this->once())
            ->method('getType')
            ->willReturn('ImportType');

        $this->mockImporterType
            ->expects($this->once())
            ->method('import')
            ->willReturn(true);

        $this->mockEventManager
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                'orderflow_import_success',
                ['import' => true, 'type' => 'ImportType']

            )
            ->willReturn(true);

        $mockRequest = $this->createMock(\RealtimeDespatch\OrderFlow\Model\Request::class);

        $this->importer->import($mockRequest);
    }
}
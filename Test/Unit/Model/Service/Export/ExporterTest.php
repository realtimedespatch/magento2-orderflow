<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Service\Export;

use Magento\Framework\Event\ManagerInterface;
use RealtimeDespatch\OrderFlow\Api\ExporterTypeInterface;
use RealtimeDespatch\OrderFlow\Model\Service\Export\Exporter;

class ExporterTest extends \PHPUnit\Framework\TestCase
{
    protected Exporter $exporter;
    protected ExporterTypeInterface $mockExporterType;
    protected ManagerInterface $mockMessageManager;

    protected function setUp(): void
    {
        $this->mockExporterType = $this->createMock(ExporterTypeInterface::class);
        $this->mockMessageManager = $this->createMock(ManagerInterface::class);
        $this->exporter = new Exporter($this->mockExporterType, $this->mockMessageManager);
    }

    public function testExportDoesNotCheckGlobalEnabledFlag(): void
    {
        $this->mockExporterType
            ->expects($this->never())
            ->method('isEnabled');

        $this->mockExporterType
            ->expects($this->once())
            ->method('getType')
            ->willReturn('InvalidType');

        $this->mockExporterType
            ->expects($this->once())
            ->method('export')
            ->willReturn('ExportedData');

        $this->mockMessageManager
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                'orderflow_export_success',
                ['export' => 'ExportedData', 'type' => 'InvalidType']
            );

        $this->assertEquals(
            'ExportedData',
            $this->exporter->export($this->createMock(\RealtimeDespatch\OrderFlow\Model\Request::class))
        );
    }

    public function testExport(): void
    {
        $this->mockExporterType
            ->expects($this->once())
            ->method('getType')
            ->willReturn('Product');

        $this->mockExporterType
            ->expects($this->once())
            ->method('export')
            ->willReturn('ExportedData');

        $this->mockMessageManager
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                'orderflow_export_success',
                ['export' => 'ExportedData', 'type' => 'Product']
            );

        $mockRequest = $this->createMock(\RealtimeDespatch\OrderFlow\Model\Request::class);

        $this->assertEquals(
            'ExportedData',
            $this->exporter->export($mockRequest)
        );
    }

    public function testExportException(): void
    {
        $this->mockExporterType
            ->expects($this->once())
            ->method('getType')
            ->willReturn('Product');

        $exception = new \Exception('ExportException');

        $this->mockExporterType
            ->expects($this->once())
            ->method('export')
            ->willThrowException($exception);

        $this->mockMessageManager
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                'orderflow_exception',
                ['exception' => $exception, 'type' => 'Product', 'process' => 'export']
            );

        $mockRequest = $this->createMock(\RealtimeDespatch\OrderFlow\Model\Request::class);
        $this->exporter->export($mockRequest);

    }

}
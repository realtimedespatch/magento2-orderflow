<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Service\Export\Type;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;
use Psr\Log\LoggerInterface;
use RealtimeDespatch\OrderFlow\Model\Export;
use RealtimeDespatch\OrderFlow\Model\ExportLine;
use RealtimeDespatch\OrderFlow\Model\Service\Export\Type\ExporterType;

abstract class AbstractExporterTypeTest extends \PHPUnit\Framework\TestCase
{
    protected ScopeConfigInterface $mockScopeConfig;
    protected LoggerInterface $mockLogger;
    protected ObjectManagerInterface $mockObjectManager;
    protected ExporterType $exporterType;
    protected Export $mockExport;
    protected ExportLine $mockExportLine;

    protected function setUp(): void
    {
        $this->mockScopeConfig = $this->createMock(ScopeConfigInterface::class);
        $this->mockLogger = $this->createMock(LoggerInterface::class);
        $this->mockObjectManager = $this->createMock(ObjectManagerInterface::class);
        $this->mockExport = $this->createMock(Export::class);
        $this->mockExportLine = $this->createMock(ExportLine::class);

        $this->mockObjectManager
            ->method('create')
            ->will($this->returnCallback(function($class) {
                if ($class == 'RealtimeDespatch\OrderFlow\Model\Export') {
                    return $this->mockExport;
                }
                if ($class == \Magento\Framework\DB\Transaction::class) {
                    return $this->createMock(\Magento\Framework\DB\Transaction::class);
                }
                if ($class = \RealtimeDespatch\OrderFlow\Model\ExportLine::class) {
                    return $this->mockExportLine;
                }
                return null;
            }));
    }

    public function testIsEnabled(): void
    {
        $this->mockScopeConfig
            ->method('getValue')
            ->with($this->getEnabledConfigPath(), \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE)
            ->willReturnOnConsecutiveCalls(true, false);

        $this->assertTrue($this->exporterType->isEnabled());
        $this->assertFalse($this->exporterType->isEnabled());
    }

    public function testGetType(): void
    {
        $this->assertEquals($this->getTypeName(), $this->exporterType->getType());
    }

    public function testExport(): void
    {
        $mockRequest = $this->createMock(\RealtimeDespatch\OrderFlow\Model\Request::class);
        $mockRequestLine = $this->createMock(\RealtimeDespatch\OrderFlow\Model\RequestLine::class);

        $mockRequest
            ->method('getLines')
            ->willReturn([$mockRequestLine]);

        $mockRequestLine
            ->method('getBody')
            ->willReturn($this->getTestExportRequestLineBody());

        $this->mockExport
            ->expects($this->once())
            ->method('addLine')
            ->with($this->mockExportLine);

        $this->preExport();
        $this->exporterType->export($mockRequest);
    }

    public function testExportWithException(): void
    {
        $mockRequest = $this->createMock(\RealtimeDespatch\OrderFlow\Model\Request::class);
        $mockRequestLine = $this->createMock(\RealtimeDespatch\OrderFlow\Model\RequestLine::class);

        $mockRequest
            ->method('getLines')
            ->willReturn([$mockRequestLine]);

        $mockRequestLine
            ->method('getBody')
            ->willReturn($this->getTestExportRequestLineBody());

        $this->mockExport
            ->method('getFailures')
            ->willReturn(0);

        $this->mockExport
            ->method('setFailures')
            ->withConsecutive([0], [1]);

        $this->mockExportLine
            ->method('setEntity')
            ->with($this->getTypeName());

        $this->mockExportLine
            ->expects($this->once())
            ->method('setMessage');

        $this->mockExportLine
            ->method('setResult')
            ->with('Failure');

        $this->preExport();
        $this->preExportWithException();
        $this->exporterType->export($mockRequest);
     }

    protected function preExport(): void
    {
    }

    protected function preExportWithException(): void
    {
    }

    abstract protected function getTestExportRequestLineBody(): object;

    abstract protected function getEnabledConfigPath(): string;

    abstract protected function getTypeName(): string;
}
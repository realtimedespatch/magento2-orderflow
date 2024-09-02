<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Service\Request\Type;

use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Model\Request;
use RealtimeDespatch\OrderFlow\Model\Service\Export\Exporter;
use RealtimeDespatch\OrderFlow\Model\Service\Request\Type\ExportRequestProcessorType;

class ExportRequestProcessorTypeTest extends TestCase
{
    protected ExportRequestProcessorType $processorType;
    protected Request $mockRequest;
    protected Exporter $mockExporter;

    protected function setUp(): void
    {
        $this->mockRequest = $this->createMock(Request::class);
        $this->mockExporter = $this->createMock(Exporter::class);
        $this->processorType = new ExportRequestProcessorType($this->mockExporter);
    }

    public function testProcess(): void
    {
        $this->mockExporter
            ->expects($this->once())
            ->method('export')
            ->with($this->mockRequest)
            ->willReturn(true);

        $result = $this->processorType->process($this->mockRequest);
        $this->assertTrue($result);
    }
}
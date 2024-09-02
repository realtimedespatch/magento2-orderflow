<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Service\Request\Type;

use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Model\Service\Export\Exporter;
use RealtimeDespatch\OrderFlow\Model\Service\Request\Type\OrderRequestProcessorType;

class OrderRequestProcessorTypeTest extends TestCase
{
    protected OrderRequestProcessorType $processorType;
    protected Exporter $mockExporter;

    protected function setUp(): void
    {
        $this->mockExporter = $this->createMock(Exporter::class);

        $this->processorType = new OrderRequestProcessorType($this->mockExporter);
    }

    public function testProcess(): void
    {
        $mockRequest = $this->createMock(\RealtimeDespatch\OrderFlow\Model\Request::class);

        $this->mockExporter
            ->expects($this->once())
            ->method('export')
            ->with($mockRequest)
            ->willReturn(true);

        $result = $this->processorType->process($mockRequest);
        $this->assertTrue($result);
    }
}
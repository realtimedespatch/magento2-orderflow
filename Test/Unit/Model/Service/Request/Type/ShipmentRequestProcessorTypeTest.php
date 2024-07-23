<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Service\Request\Type;

use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Model\Service\Export\Exporter;
use RealtimeDespatch\OrderFlow\Model\Service\Import\Importer;
use RealtimeDespatch\OrderFlow\Model\Service\Request\Type\ProductRequestProcessorType;
use RealtimeDespatch\OrderFlow\Model\Service\Request\Type\ShipmentRequestProcessorType;

class ShipmentRequestProcessorTypeTest extends TestCase
{
    protected ShipmentRequestProcessorType $processorType;
    protected Importer $mockImporter;

    protected function setUp(): void
    {
        $this->mockImporter = $this->createMock(Importer::class);
        $this->processorType = new ShipmentRequestProcessorType($this->mockImporter);
    }

    public function testProcess(): void
    {
        $mockRequest = $this->createMock(\RealtimeDespatch\OrderFlow\Model\Request::class);

        $this->mockImporter
            ->expects($this->once())
            ->method('import')
            ->with($mockRequest)
            ->willReturn(true);

        $result = $this->processorType->process($mockRequest);
        $this->assertTrue($result);
    }
}
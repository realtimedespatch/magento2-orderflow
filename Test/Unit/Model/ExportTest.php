<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model;

use RealtimeDespatch\OrderFlow\Model\Export;
use RealtimeDespatch\OrderFlow\Model\ExportLine;

class ExportTest extends AbstractModelTest
{
    protected Export $export;

    protected function setUp(): void
    {
        parent::setUp();

        $this->idFieldName = 'export_id';

        $this->export = new Export(
            $this->mockContext,
            $this->mockRegistry,
            $this->mockResource,
            $this->mockResourceCollection
        );
    }

    public function testData(): void
    {
        $mockExportLines = [
            $this->createMock(ExportLine::class),
            $this->createMock(ExportLine::class),
        ];

        $this->export->setData([
            'export_id' => 1,
        ]);

        $this->assertEquals(1, $this->export->getId());

        $this->export->setRequestId(2);
        $this->assertEquals(2, $this->export->getRequestId());

        $this->export->setLines($mockExportLines);
        $this->assertEquals($mockExportLines, $this->export->getLines());

        $this->export->setMessageId(123);
        $this->assertEquals(123, $this->export->getMessageId());

        $this->export->setScopeId(456);
        $this->assertEquals(456, $this->export->getScopeId());

        $this->export->setEntity('Test Entity');
        $this->assertEquals('Test Entity', $this->export->getEntity());

        $this->export->setOperation('Test Operation');
        $this->assertEquals('Test Operation', $this->export->getOperation());

        $this->export->setSuccesses(10);
        $this->assertEquals(10, $this->export->getSuccesses());

        $this->export->setFailures(20);
        $this->assertEquals(20, $this->export->getFailures());

        $this->export->setSuperseded(30);
        $this->assertEquals(30, $this->export->getSuperseded());

        $this->export->setDuplicates(40);
        $this->assertEquals(40, $this->export->getDuplicates());

        $this->export->setCreatedAt('2021-01-01 00:00:00');
        $this->assertEquals('2021-01-01 00:00:00', $this->export->getCreatedAt());

        $this->export->setViewedAt('2022-01-01 00:00:00');
        $this->assertEquals('2022-01-01 00:00:00', $this->export->getViewedAt());

        $this->assertFalse($this->export->isProductExport());
        $this->export->setEntity('Product');
        $this->assertTrue($this->export->isProductExport());

        $this->assertFalse($this->export->isOrderExport());
        $this->export->setEntity('Order');
        $this->assertTrue($this->export->isOrderExport());

        $this->assertEquals(2, count($this->export->getLines()));
        $this->export->addLine($this->createMock(ExportLine::class));
        $this->assertEquals(3, count($this->export->getLines()));
    }
}
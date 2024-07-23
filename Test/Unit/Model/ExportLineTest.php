<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model;

use RealtimeDespatch\OrderFlow\Model\Export;
use RealtimeDespatch\OrderFlow\Model\ExportLine;

class ExportLineTest extends AbstractModelTest
{
    protected ExportLine $exportLine;

    protected function setUp(): void
    {
        parent::setUp();

        $this->idFieldName = 'line_id';

        $this->exportLine = new ExportLine(
            $this->mockContext,
            $this->mockRegistry,
            $this->mockResource,
            $this->mockResourceCollection
        );
    }

    public function testData(): void
    {
        $this->exportLine->setData('line_id', 1);
        $this->assertEquals(1, $this->exportLine->getId());

        $this->exportLine->setExportId(2);
        $this->assertEquals(2, $this->exportLine->getExportId());

        $this->exportLine->setResult('success');
        $this->assertEquals('success', $this->exportLine->getResult());

        $this->exportLine->setEntity('order');
        $this->assertEquals('order', $this->exportLine->getEntity());

        $this->exportLine->setReference('1000001');
        $this->assertEquals('1000001', $this->exportLine->getReference());

        $this->exportLine->setOperation('update');
        $this->assertEquals('update', $this->exportLine->getOperation());

        $this->exportLine->setCreatedAt('2021-01-01 00:00:00');
        $this->assertEquals('2021-01-01 00:00:00', $this->exportLine->getCreatedAt());

        $this->exportLine->setProcessedAt('2022-01-01 00:00:00');
        $this->assertEquals('2022-01-01 00:00:00', $this->exportLine->getProcessedAt());

        $this->exportLine->setMessage('Test Message');
        $this->assertEquals('Test Message', $this->exportLine->getMessage());

        $this->exportLine->setDetail('Test Detail');
        $this->assertEquals('Test Detail', $this->exportLine->getDetail());

        $this->exportLine->setAdditionalData('Test Additional Data');
        $this->assertEquals('Test Additional Data', $this->exportLine->getAdditionalData());

        $this->assertFalse($this->exportLine->isExport());
        $this->exportLine->setOperation('Export');
        $this->assertTrue($this->exportLine->isExport());

        $this->assertFalse($this->exportLine->isCancellation());
        $this->exportLine->setOperation('Cancel');
        $this->assertTrue($this->exportLine->isCancellation());

        $this->assertFalse($this->exportLine->isSuccess());
        $this->exportLine->setResult('Success');
        $this->assertTrue($this->exportLine->isSuccess());

        $this->assertFalse($this->exportLine->isFailure());
        $this->exportLine->setResult('Failure');
        $this->assertTrue($this->exportLine->isFailure());


        $this->assertEquals('Failed', $this->exportLine->getEntityExportStatus());
        $this->exportLine->setResult('Success');

        $this->assertEquals('Cancelled', $this->exportLine->getEntityExportStatus());

        $this->exportLine->setOperation('Export');
        $this->assertEquals('Exported', $this->exportLine->getEntityExportStatus());

        $this->exportLine->setResult(null);
        $this->exportLine->setOperation('Cancellation');
        $this->assertEquals('Queued', $this->exportLine->getEntityExportStatus());

    }
}
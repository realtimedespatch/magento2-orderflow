<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model;

use RealtimeDespatch\OrderFlow\Model\ImportLine;

class ImportLineTest extends AbstractModelTest
{
    protected ImportLine $importLine;

    protected function setUp(): void
    {
        parent::setUp();

        $this->idFieldName = 'line_id';

        $this->importLine = new ImportLine(
            $this->mockContext,
            $this->mockRegistry,
            $this->mockResource,
            $this->mockResourceCollection
        );
    }

    public function testData(): void
    {
        $this->importLine->setData(['line_id' => 1]);
        $this->assertEquals(1, $this->importLine->getId());

        $this->importLine->setImportId(2);
        $this->assertEquals(2, $this->importLine->getImportId());

        $this->importLine->setEntity('Order');
        $this->assertEquals('Order', $this->importLine->getEntity());

        $this->importLine->setReference('1000001');
        $this->assertEquals('1000001', $this->importLine->getReference());

        $this->importLine->setOperation('Create');
        $this->assertEquals('Create', $this->importLine->getOperation());

        $this->importLine->setResult('Success');
        $this->assertEquals('Success', $this->importLine->getResult());

        $this->importLine->setMessage('Test Message');
        $this->assertEquals('Test Message', $this->importLine->getMessage());

        $this->importLine->setSequenceId(3);
        $this->assertEquals(3, $this->importLine->getSequenceId());

        $this->importLine->setAdditionalData('Test Detail');
        $this->assertEquals('Test Detail', $this->importLine->getAdditionalData());

        $this->importLine->setCreatedAt('2021-01-01 00:00:00');
        $this->assertEquals('2021-01-01 00:00:00', $this->importLine->getCreatedAt());

        $this->importLine->setProcessedAt('2022-01-01 00:00:00');
        $this->assertEquals('2022-01-01 00:00:00', $this->importLine->getProcessedAt());
    }
}